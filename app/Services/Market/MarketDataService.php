<?php

namespace App\Services\Market;

use App\Models\MarketInstrument;
use App\Models\MarketSnapshot;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * Foreks benzeri piyasa veri simülatörü (Bölüm 5).
 *
 * Enstrüman kataloğu `market_instruments` tablosundan gelir; canlı değerler
 * deterministik bir salınım + mikro-gürültü ile üretilir. Aynı zaman damgası
 * için aynı sonucu verir (test edilebilirlik), birkaç saniye sonra ise küçük
 * oynamalarla "canlı pano" hissi sağlar.
 *
 * CH-2: kripto grubunda `market_snapshots` tablosunda taze (config
 * `market.coingecko.max_age_minutes` içinde) CoinGecko satırı varsa quote o
 * satırdan üretilir; tazelik geçerse simülatöre düşülür. Ağ/anahtar yoksa
 * tablo boş kalır ve davranış CH-2 öncesiyle birebir aynıdır.
 */
class MarketDataService
{
    /**
     * İstek boyunca kataloğu tek seferlik önbelleğe alır.
     *
     * @var Collection<int, MarketInstrument>|null
     */
    private ?Collection $catalog = null;

    /**
     * Kripto snapshot önbelleği (market_instrument_id => MarketSnapshot).
     *
     * @var Collection<int, MarketSnapshot>|null
     */
    private ?Collection $cryptoSnapshots = null;

    /** Snapshot önbelleğinin yüklenip yüklenmediği (boş tabloda tekrar sorma). */
    private bool $cryptoSnapshotsLoaded = false;

    /**
     * @return Collection<int, MarketInstrument>
     */
    public function all(): Collection
    {
        return $this->catalog ??= MarketInstrument::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->orderBy('code')
            ->get();
    }

    /**
     * @return Collection<int, MarketInstrument>
     */
    public function instruments(?string $group = null): Collection
    {
        if ($group === null) {
            return $this->all();
        }

        return $this->all()->where('group', $group)->values();
    }

    /**
     * @return array<int, string>
     */
    public function groups(): array
    {
        return MarketInstrument::GROUPS;
    }

    /**
     * Tek enstrümanın güncel anlık görüntüsü.
     *
     * Kripto grubunda taze CoinGecko snapshot'ı varsa ondan üretilir (CH-2).
     */
    public function quote(MarketInstrument|string $instrument, ?CarbonInterface $at = null): Quote
    {
        if (is_string($instrument)) {
            $instrument = $this->all()->firstWhere('code', $instrument)
                ?? MarketInstrument::where('code', $instrument)->firstOrFail();
        }

        $at = $at ? CarbonImmutable::instance($at) : CarbonImmutable::now();

        if ($instrument->group === MarketInstrument::GROUP_CRYPTO) {
            $live = $this->liveQuote($instrument, $at);

            if ($live !== null) {
                return $live;
            }
        }

        $t = (float) $at->timestamp;
        $phase = crc32($instrument->code.':'.$instrument->group);

        $slow = sin(($t / 21600.0) * M_PI * 2 + ($phase % 628) / 100);
        $fast = sin(($t / 41.0) * M_PI * 2 + ($phase % 314) / 1);
        $micro = sin(($t / 7.0) * M_PI * 2 + ($phase % 100) / 1);

        $base = (float) ($instrument->base_price ?: 1);
        $previousClose = (float) ($instrument->previous_close ?: $base);

        $pct = 0.006 * $slow + 0.0025 * $fast + 0.0009 * $micro;
        $last = max($base * (1 + $pct), $base * 0.5);

        $change = $last - $previousClose;
        $changePercent = $previousClose != 0.0 ? ($change / $previousClose) * 100 : 0.0;

        $monthlyPercent = $this->signed($phase + 11) * 14;
        $yearlyPercent = $this->signed($phase + 23) * 48;

        $open = $base * (1 + 0.002 * $slow);

        $spread = 0.0015 + 0.001 * abs($this->signed($phase + 31));
        $bid = $last * (1 - $spread);
        $ask = $last * (1 + $spread);

        $high = max($last, $open, $previousClose) * (1 + 0.0015 + 0.003 * abs($this->signed($phase + 41)));
        $low = min($last, $open, $previousClose) * (1 - 0.0015 - 0.003 * abs($this->signed($phase + 43)));

        $volumeLot = (int) ($this->unit($phase + 53) * 4_500_000) + 1_000;
        $volumeTl = $volumeLot * $last;

        $meta = (array) ($instrument->meta ?? []);
        $supply = (float) ($meta['supply'] ?? 0);
        $marketCap = $supply > 0 ? $last * $supply : 0.0;
        $volume24h = (float) ($meta['volume_24h'] ?? 0);
        if ($volume24h <= 0 && $marketCap > 0) {
            $volume24h = $marketCap * (0.02 + 0.05 * $this->unit($phase + 61));
        }
        $change7d = $this->signed($phase + 71) * 12;

        return new Quote(
            instrument: $instrument,
            last: round($last, $instrument->decimals),
            previousClose: round($previousClose, $instrument->decimals),
            change: round($change, $instrument->decimals),
            changePercent: round($changePercent, 2),
            monthlyPercent: round($monthlyPercent, 2),
            yearlyPercent: round($yearlyPercent, 2),
            high: round($high, $instrument->decimals),
            low: round($low, $instrument->decimals),
            open: round($open, $instrument->decimals),
            bid: round($bid, $instrument->decimals),
            ask: round($ask, $instrument->decimals),
            volumeLot: $volumeLot,
            volumeTl: round($volumeTl, 2),
            volume24h: round($volume24h, 2),
            marketCap: round($marketCap, 2),
            change7d: round($change7d, 2),
            updatedAt: $at,
        );
    }

    /**
     * Bir grup (veya tümü) için canlı fiyat anlık görüntüleri.
     *
     * @return Collection<int, Quote>
     */
    public function quotes(?string $group = null, ?CarbonInterface $at = null): Collection
    {
        return $this->instruments($group)->map(fn (MarketInstrument $i): Quote => $this->quote($i, $at))->values();
    }

    /**
     * Gruplara göre anlık görüntü (JSON ucundaki gövde).
     *
     * @param  array<int, string>|null  $groups
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function snapshot(?array $groups = null, ?CarbonInterface $at = null): array
    {
        $groups ??= array_keys($this->groups());
        $out = [];

        foreach ($groups as $group) {
            $out[$group] = $this->quotes($group, $at)->map->toArray()->all();
        }

        return $out;
    }

    /**
     * Kaynak notu (Foreks / CME gecikme bildirimi — Bölüm 5, 11.6).
     */
    public function sourceNote(string $source): ?string
    {
        return config("market.sources.{$source}.note");
    }

    /**
     * Kaynak veri lisansı metni (ör. CME Chicago Mercantile Exchange lisansı —
     * Bölüm 11.6, §15.5).
     */
    public function licenseNote(string $source): ?string
    {
        return config("market.sources.{$source}.license");
    }

    public function lastUpdated(?CarbonInterface $at = null): string
    {
        $at ??= CarbonImmutable::now();

        return $at->format((string) config('market.timestamp_format', 'Y/m/d H:i:s'));
    }

    /**
     * Bir enstrüman için deterministik güniçi seri (mini grafik/sparkline).
     *
     * Kripto grubunda taze CoinGecko snapshot'ının sparkline_7d serisi
     * varsa ondan örneklenir (CH-2); yoksa simülatör seri kullanılır.
     *
     * @return array<int, float>
     */
    public function sparkline(MarketInstrument|string $instrument, int $points = 24, int $intervalSeconds = 900, ?CarbonInterface $at = null): array
    {
        if (is_string($instrument)) {
            $instrument = $this->all()->firstWhere('code', $instrument)
                ?? MarketInstrument::where('code', $instrument)->firstOrFail();
        }

        $at = $at ? CarbonImmutable::instance($at) : CarbonImmutable::now();

        if ($instrument->group === MarketInstrument::GROUP_CRYPTO) {
            $series = $this->liveSparkline($instrument, $at, $points);

            if ($series !== null) {
                return $series;
            }
        }

        $values = [];

        for ($i = $points - 1; $i >= 0; $i--) {
            $values[] = $this->quote($instrument, $at->subSeconds($i * $intervalSeconds))->last;
        }

        return $values;
    }

    /**
     * Referans Highcharts alan grafiğini SSR basar (market widget büyük grafik).
     * Referans DOM'uyla aynı class/renk/ölçü yapısı; JS gerektirmez.
     */
    public function highchartsArea(MarketInstrument|string $instrument, int $points = 64, int $width = 414, int $height = 201, string $uid = '0'): string
    {
        $values = $this->sparkline($instrument, $points);

        return app(HighchartsRenderer::class)->areaChart($values, $width, $height, $uid);
    }

    /**
     * Güniçi seriyi satır içi SVG polyline olarak üretir (Chart.js yokluğunda
     * SSR karşılığı; renk yönü ilk/son değere göre Bölüm 17 token'larıyla).
     */
    public function sparklineSvg(MarketInstrument|string $instrument, int $points = 24, int $width = 96, int $height = 32): string
    {
        $values = $this->sparkline($instrument, $points);
        $min = min($values);
        $max = max($values);
        $range = $max - $min ?: 1.0;
        $stepX = $points > 1 ? $width / ($points - 1) : $width;
        $coords = [];

        foreach ($values as $i => $value) {
            $x = round($i * $stepX, 2);
            $y = round($height - (($value - $min) / $range) * ($height - 4) - 2, 2);
            $coords[] = $x.','.$y;
        }

        $direction = end($values) > $values[0] ? 'up' : (end($values) < $values[0] ? 'down' : 'flat');
        $color = ['up' => '#24A278', 'down' => '#BD1B2E', 'flat' => '#9D9D9D'][$direction];

        return '<svg class="sparkline sparkline-'.$direction.'" width="'.$width.'" height="'.$height
            .'" viewBox="0 0 '.$width.' '.$height.'" preserveAspectRatio="none" fill="none" aria-hidden="true">'
            .'<polyline points="'.implode(' ', $coords).'" stroke="'.$color.'" stroke-width="1.5" '
            .'fill="none" stroke-linejoin="round" stroke-linecap="round"/></svg>';
    }

    /**
     * Taze CoinGecko snapshot'ından kripto quote (CH-2).
     *
     * Snapshot yoksa / tazeliği geçtiyse null döner; çağıran simülatöre düşer.
     */
    private function liveQuote(MarketInstrument $instrument, CarbonImmutable $at): ?Quote
    {
        $snapshot = $this->freshSnapshot($instrument, $at);

        if ($snapshot === null) {
            return null;
        }

        $decimals = (int) ($instrument->decimals ?? 2);
        $last = (float) $snapshot->price;
        $change24h = (float) ($snapshot->change_24h ?? 0.0);
        $previousClose = $last / (1 + $change24h / 100);
        $change = $last - $previousClose;

        $spread = 0.0008;
        $volume24h = (float) ($snapshot->volume_24h ?? 0.0);
        $marketCap = (float) ($snapshot->market_cap ?? 0.0);
        $change7d = (float) ($snapshot->change_7d ?? 0.0);

        return new Quote(
            instrument: $instrument,
            last: round($last, $decimals),
            previousClose: round($previousClose, $decimals),
            change: round($change, $decimals),
            changePercent: round($change24h, 2),
            monthlyPercent: round($change7d * 4, 2), // yaklaşımlık 30g (CoinGecko 7g veriyor)
            yearlyPercent: 0.0, // yıllık serisi API'de yok; 0 yerine "—" template'i bağlı değil
            high: round((float) ($snapshot->high_24h ?? $last), $decimals),
            low: round((float) ($snapshot->low_24h ?? $last), $decimals),
            open: round($previousClose, $decimals),
            bid: round($last * (1 - $spread), $decimals),
            ask: round($last * (1 + $spread), $decimals),
            volumeLot: $last > 0 ? (int) round($volume24h / $last) : 0,
            volumeTl: round($volume24h, 2),
            volume24h: round($volume24h, 2),
            marketCap: round($marketCap, 2),
            change7d: round($change7d, 2),
            updatedAt: $snapshot->captured_at ?? $at,
        );
    }

    /**
     * Taze CoinGecko sparkline_7d serisinden istenen nokta sayısına
     * örneklenmiş dizi (CH-2). Taze seri yoksa null.
     *
     * @return array<int, float>|null
     */
    private function liveSparkline(MarketInstrument $instrument, CarbonImmutable $at, int $points): ?array
    {
        $snapshot = $this->freshSnapshot($instrument, $at);
        $series = $snapshot?->sparkline_7d;

        if (! is_array($series) || count($series) < 2 || $points < 2) {
            return null;
        }

        $count = count($series);
        $out = [];

        for ($i = 0; $i < $points; $i++) {
            $idx = (int) round($i * ($count - 1) / ($points - 1));
            $out[] = (float) $series[$idx];
        }

        return $out;
    }

    /**
     * Enstrümanın taze snapshot'ı (config `market.coingecko.max_age_minutes`
     * içinde yakalanmış olmalı); istek boyunca önbelleklenir.
     */
    private function freshSnapshot(MarketInstrument $instrument, CarbonImmutable $at): ?MarketSnapshot
    {
        if (! $this->cryptoSnapshotsLoaded) {
            $this->cryptoSnapshots = $this->tableExists()
                ? MarketSnapshot::latestForGroup(MarketInstrument::GROUP_CRYPTO)
                : collect();
            $this->cryptoSnapshotsLoaded = true;
        }

        $snapshot = $this->cryptoSnapshots?->get($instrument->id);

        if ($snapshot === null) {
            return null;
        }

        $maxAge = (int) config('market.coingecko.max_age_minutes', 15);
        $capturedAt = $snapshot->captured_at;

        if ($capturedAt === null || $capturedAt->lt($at->subMinutes($maxAge)) || $capturedAt->gt($at->addMinute())) {
            return null;
        }

        return $snapshot;
    }

    /**
     * migration henüz koşulmamış ortamlarda (eski kopyalar) query'yi kırma.
     */
    private function tableExists(): bool
    {
        return Schema::hasTable('market_snapshots');
    }

    /**
     * 0..1 aralığında deterministik sözde-rastgele değer.
     */
    private function unit(int $seed): float
    {
        $x = (($seed & 0xFFFF) * 2654435761) & 0xFFFFFFFF;
        $x ^= $x >> 15;
        $x = (($x & 0xFFFF) * 2246822519) & 0xFFFFFFFF;
        $x ^= $x >> 13;

        return ($x & 0xFFFFFF) / 0xFFFFFF;
    }

    /**
     * -1..1 aralığında deterministik sözde-rastgele değer.
     */
    private function signed(int $seed): float
    {
        return $this->unit($seed) * 2 - 1;
    }
}
