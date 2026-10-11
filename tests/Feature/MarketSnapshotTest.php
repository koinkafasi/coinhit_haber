<?php

namespace Tests\Feature;

use App\Models\MarketInstrument;
use App\Models\MarketSnapshot;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

/**
 * CH-2 — CoinGecko kripto piyasa veri motoru.
 *
 * Ağ her zaman fake'lenir; gerçek API'ye asla çıkılmaz.
 */
class MarketSnapshotTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        config(['market.coingecko.api_key' => 'CG-test-key']);
    }

    /** CoinGecko /coins/markets sahte yanıtı (2 satır). */
    private function fakeMarkets(): array
    {
        return [
            [
                'id' => 'bitcoin',
                'current_price' => 123456.78,
                'market_cap' => 2_400_000_000_000.0,
                'total_volume' => 45_000_000_000.0,
                'high_24h' => 125000.0,
                'low_24h' => 120000.0,
                'price_change_percentage_24h_in_currency' => 2.5,
                'price_change_percentage_7d_in_currency' => -1.25,
                'sparkline_in_7d' => ['price' => [100.0, 110.0, 120.0, 130.0]],
            ],
            [
                'id' => 'ethereum',
                'current_price' => 4321.09,
                'market_cap' => 520_000_000_000.0,
                'total_volume' => 21_000_000_000.0,
                'high_24h' => 4400.0,
                'low_24h' => 4200.0,
                'price_change_percentage_24h_in_currency' => -0.8,
                'price_change_percentage_7d_in_currency' => 3.3,
                'sparkline_in_7d' => ['price' => [10.0, 20.0, 30.0, 40.0]],
            ],
        ];
    }

    public function test_snapshot_command_writes_rows_from_coingecko_payload(): void
    {
        Http::fake([
            'api.coingecko.com/*' => Http::response($this->fakeMarkets()),
        ]);

        Artisan::call('market:snapshot');

        $this->assertSame(2, MarketSnapshot::count());

        $btc = MarketSnapshot::whereHas('instrument', fn ($q) => $q->where('code', 'BTC'))->first();
        $this->assertNotNull($btc);
        $this->assertSame(123456.78, $btc->price);
        $this->assertSame(2.5, $btc->change_24h);
        $this->assertSame(-1.25, $btc->change_7d);
        $this->assertEqualsWithDelta([100.0, 110.0, 120.0, 130.0], $btc->sparkline_7d, 0.001);
        $this->assertSame('coingecko', $btc->source);
    }

    public function test_kripto_quote_uses_fresh_snapshot(): void
    {
        $btc = MarketInstrument::where('group', MarketInstrument::GROUP_CRYPTO)->where('code', 'BTC')->firstOrFail();

        MarketSnapshot::create([
            'market_instrument_id' => $btc->id,
            'price' => 99999.0,
            'market_cap' => 1_000_000.0,
            'volume_24h' => 5_000_000.0,
            'change_24h' => 5.0,
            'change_7d' => 10.0,
            'high_24h' => 100500.0,
            'low_24h' => 98000.0,
            'sparkline_7d' => [1.0, 2.0, 3.0, 4.0],
            'source' => 'coingecko',
            'captured_at' => now(),
        ]);

        $html = $this->get('/kripto')->assertOk()->getContent();

        // Taze snapshot fiyatı (99.999,00 TR biçimi → sayfada "99.999")
        $this->assertStringContainsString('99.999', $html);
    }

    public function test_stale_snapshot_falls_back_to_simulator(): void
    {
        $btc = MarketInstrument::where('group', MarketInstrument::GROUP_CRYPTO)->where('code', 'BTC')->firstOrFail();

        MarketSnapshot::create([
            'market_instrument_id' => $btc->id,
            'price' => 1.0, // gerçekçi olmayan eski değer
            'change_24h' => 0.0,
            'source' => 'coingecko',
            'captured_at' => now()->subHours(3), // max_age 15 dk
        ]);

        $service = app(\App\Services\Market\MarketDataService::class);
        $quote = $service->quote($btc);

        // Simülatör base_price (95.000) civarı üretecektir; 1.0 değil.
        $this->assertGreaterThan(1000.0, $quote->last);
    }

    public function test_command_without_api_key_is_noop(): void
    {
        config(['market.coingecko.api_key' => '']);

        Http::fake(); // çağrı hiç yapılmamalı

        Artisan::call('market:snapshot');

        $this->assertSame(0, MarketSnapshot::count());
        Http::assertNothingSent();
    }

    public function test_command_survives_api_error(): void
    {
        Http::fake([
            'api.coingecko.com/*' => Http::response('rate limited', 429),
        ]);

        Artisan::call('market:snapshot');

        $this->assertSame(0, MarketSnapshot::count());
    }

    public function test_schedule_registers_market_snapshot(): void
    {
        $events = app(\Illuminate\Console\Scheduling\Schedule::class)->events();
        $found = collect($events)->contains(
            fn ($e) => str_contains($e->command ?? '', 'market:snapshot')
        );

        $this->assertTrue($found, 'market:snapshot cron\'a kayıtlı olmalı');
    }
}
