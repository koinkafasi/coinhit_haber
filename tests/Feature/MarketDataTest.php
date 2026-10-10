<?php

namespace Tests\Feature;

use App\Models\MarketInstrument;
use App\Services\Market\MarketDataService;
use Carbon\CarbonImmutable;
use Database\Seeders\MarketInstrumentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(MarketInstrumentSeeder::class);
    }

    public function test_catalog_is_seeded_with_all_groups(): void
    {
        $this->assertGreaterThan(200, MarketInstrument::count());
        $this->assertEquals(40, MarketInstrument::where('group', MarketInstrument::GROUP_BIST_INDEX)->count());
        $this->assertEquals(14, MarketInstrument::where('group', MarketInstrument::GROUP_CURRENCY)->count());
        $this->assertEquals(17, MarketInstrument::where('group', MarketInstrument::GROUP_GOLD)->count());
        $this->assertEquals(20, MarketInstrument::where('group', MarketInstrument::GROUP_CRYPTO)->count());
        $this->assertEquals(11, MarketInstrument::where('group', MarketInstrument::GROUP_EUROBOND)->count());
        $this->assertEquals(12, MarketInstrument::where('group', MarketInstrument::GROUP_INTL_BOND)->count());
    }

    public function test_quote_is_deterministic_for_a_fixed_timestamp(): void
    {
        $service = app(MarketDataService::class);
        $at = CarbonImmutable::parse('2026-10-10 12:00:00');

        $first = $service->quote('XU100', $at);
        $second = $service->quote('XU100', $at);

        $this->assertSame($first->last, $second->last);
        $this->assertSame($first->change, $second->change);
    }

    public function test_quote_changes_over_time_to_simulate_live_data(): void
    {
        $service = app(MarketDataService::class);

        $a = $service->quote('XU100', CarbonImmutable::parse('2026-10-10 12:00:00'));
        $b = $service->quote('XU100', CarbonImmutable::parse('2026-10-10 12:00:45'));

        $this->assertNotSame($a->last, $b->last);
    }

    public function test_formatting_rules_are_applied(): void
    {
        $service = app(MarketDataService::class);

        $usd = $service->quote('USDTRY');
        $this->assertStringContainsString(',', $usd->formattedLast());
        $this->assertSame(4, $usd->decimals());

        $jpy = $service->quote('USDJPY');
        $this->assertSame(2, $jpy->decimals());
    }

    public function test_gold_has_bid_ask_and_time(): void
    {
        $quote = app(MarketDataService::class)->quote('CEYREK');

        $this->assertLessThan($quote->last, $quote->bid);
        $this->assertGreaterThan($quote->last, $quote->ask);
        $this->assertMatchesRegularExpression('/^\d{2}:\d{2}$/', $quote->time());
    }

    public function test_crypto_has_market_cap_and_volume(): void
    {
        $btc = app(MarketDataService::class)->quote('BTC');

        $this->assertGreaterThan(0, $btc->marketCap);
        $this->assertGreaterThan(0, $btc->volume24h);
        $this->assertStringStartsWith('$', $btc->formattedMarketCap());
    }

    public function test_delay_labels_follow_source(): void
    {
        $service = app(MarketDataService::class);

        $bist = $service->quote('XU100');
        $this->assertSame(15, $bist->instrument->delay_minutes);

        $crypto = $service->quote('BTC');
        $this->assertSame(0, $crypto->instrument->delay_minutes);
    }

    public function test_snapshot_groups_quotes(): void
    {
        $snapshot = app(MarketDataService::class)->snapshot(['bist_index', 'crypto']);

        $this->assertArrayHasKey('bist_index', $snapshot);
        $this->assertArrayHasKey('crypto', $snapshot);
        $this->assertCount(40, $snapshot['bist_index']);
    }
}