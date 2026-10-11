<?php

namespace App\Console\Commands;

use App\Models\MarketInstrument;
use App\Models\MarketSnapshot;
use App\Services\Market\CoinGeckoClient;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

/**
 * CoinGecko'dan kripto fiyatlarını çekip `market_snapshots` tablosuna yazar
 * (CH-2). `routes/console.php` üzerinden saatlik planlanır; elle de
 * `php artisan market:snapshot` ile çalıştırılabilir.
 */
class SnapshotMarket extends Command
{
    protected $signature = 'market:snapshot {--keep=168 : Saklanacak en eski saat (168 = 7 gün)}';

    protected $description = 'CoinGecko API ile kripto fiyat anlık görüntüsü çeker ve market_snapshots tablosuna yazar';

    public function handle(CoinGeckoClient $client): int
    {
        $rows = $client->markets();

        if ($rows === []) {
            $this->warn('CoinGecko boş yanıt verdi (anahtar/ağ); mevcut snapshot\'lar korunuyor.');

            return self::SUCCESS;
        }

        $now = CarbonImmutable::now();
        $byCode = collect($rows)->keyBy(fn (array $row): string => CoinGeckoClient::ID_MAP[$row['id'] ?? ''] ?? '');

        $written = 0;

        MarketInstrument::where('group', MarketInstrument::GROUP_CRYPTO)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->each(function (MarketInstrument $instrument) use ($byCode, $now, &$written): void {
                $row = $byCode->get($instrument->code);

                if (! is_array($row) || ! isset($row['current_price'])) {
                    return;
                }

                $sparkline = data_get($row, 'sparkline_in_7d.price');
                $sparkline = is_array($sparkline)
                    ? Collection::make($sparkline)->map(fn ($v): float => (float) $v)->values()->all()
                    : null;

                MarketSnapshot::create([
                    'market_instrument_id' => $instrument->id,
                    'price' => (float) $row['current_price'],
                    'market_cap' => isset($row['market_cap']) ? (float) $row['market_cap'] : null,
                    'volume_24h' => isset($row['total_volume']) ? (float) $row['total_volume'] : null,
                    'change_24h' => isset($row['price_change_percentage_24h_in_currency'])
                        ? (float) $row['price_change_percentage_24h_in_currency'] : null,
                    'change_7d' => isset($row['price_change_percentage_7d_in_currency'])
                        ? (float) $row['price_change_percentage_7d_in_currency'] : null,
                    'high_24h' => isset($row['high_24h']) ? (float) $row['high_24h'] : null,
                    'low_24h' => isset($row['low_24h']) ? (float) $row['low_24h'] : null,
                    'sparkline_7d' => $sparkline,
                    'source' => 'coingecko',
                    'captured_at' => $now,
                ]);

                $written++;
            });

        $this->info("{$written} snapshot yazıldı ({$now->format('Y-m-d H:i:s')}).");

        $this->prune((int) $this->option('keep'));

        return self::SUCCESS;
    }

    /**
     * Eski snapshot'ları budar; tablo sınırsız büyümesin.
     */
    private function prune(int $keepHours): void
    {
        if ($keepHours < 1) {
            return;
        }

        $cutoff = CarbonImmutable::now()->subHours($keepHours);
        $deleted = MarketSnapshot::where('captured_at', '<', $cutoff)->delete();

        if ($deleted > 0) {
            $this->line("{$deleted} eski snapshot silindi ({$cutoff->format('Y-m-d H:i:s')} öncesi).");
        }
    }
}
