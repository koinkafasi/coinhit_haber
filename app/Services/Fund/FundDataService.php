<?php

namespace App\Services\Fund;

use App\Models\Fund;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

/**
 * Yatırım fonu veri simülatörü (spec §5 fon verisi, §8 `/yatirim-fonlari`).
 *
 * Fon fiyatı ve TL/USD değişimleri katalogdan gelir; gösterim değerleri
 * deterministik bir canlı salınımla güncellenir ("canlı sahte veri").
 */
class FundDataService
{
    /**
     * Bir fonun canlı anlık görüntüsü.
     *
     * @return array<string, mixed>
     */
    public function snapshot(Fund $fund, ?CarbonInterface $at = null): array
    {
        $at = $at ? CarbonImmutable::instance($at) : CarbonImmutable::now();
        $t = (float) $at->timestamp;
        $phase = crc32($fund->code);

        $slow = sin(($t / 18000.0) * M_PI * 2 + ($phase % 628) / 100);
        $pulse = 0.35 * sin(($t / 240.0) * M_PI * 2 + ($phase % 157) / 50);

        $price = max((float) $fund->price * (1 + 0.002 * $slow), 0.000001);
        $changeTl = (float) $fund->change_tl_percent + $pulse;
        $changeUsd = (float) $fund->change_usd_percent + $pulse * 0.7;

        $returns = [];
        foreach (Fund::RETURN_COLUMNS as $column) {
            $field = $column['field'];
            $returns[$field] = (float) $fund->{$field};
        }
        $returns['return_daily'] = round($changeTl, 2);

        return [
            'fund' => $fund,
            'code' => $fund->code,
            'name' => $fund->name,
            'url' => $fund->url,
            'price' => $price,
            'price_formatted' => $this->formatPrice($price),
            'change_tl' => round($changeTl, 2),
            'change_usd' => round($changeUsd, 2),
            'change_tl_formatted' => $this->formatPercent($changeTl),
            'change_usd_formatted' => $this->formatPercent($changeUsd),
            'direction' => $this->direction($changeTl),
            'returns' => $returns,
            'risk_value' => (int) $fund->risk_value,
            'risk_label' => $fund->risk_label,
        ];
    }

    /**
     * Bir fon sınıfındaki lider fonları döner (Yükselen/Düşen).
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function leaderboard(string $class, string $direction = 'yukselen', int $take = 10, ?CarbonInterface $at = null): Collection
    {
        $funds = Fund::where('is_active', true)
            ->where('fund_class', $class)
            ->orderBy('sort_order')
            ->get();

        return $funds
            ->map(fn (Fund $fund): array => $this->snapshot($fund, $at))
            ->sortBy(fn (array $row): float => $direction === 'dusen' ? $row['change_tl'] : -$row['change_tl'])
            ->take($take)
            ->values();
    }

    /**
     * Tüm fonların anlık görüntüsü (Karşılaştırmalı Fon Getirileri tablosu).
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function all(?CarbonInterface $at = null): Collection
    {
        return Fund::where('is_active', true)
            ->orderBy('code')
            ->get()
            ->map(fn (Fund $fund): array => $this->snapshot($fund, $at))
            ->values();
    }

    /**
     * Günün Özeti KPI bloğu (canlı sahte veri).
     *
     * @return array<string, mixed>
     */
    public function kpi(?CarbonInterface $at = null): array
    {
        $funds = Fund::where('is_active', true)->get();
        $totalValue = (float) $funds->sum('portfolio_value');
        $weighted = 0.0;

        foreach ($funds as $fund) {
            $weighted += (float) $fund->change_tl_percent * (float) $fund->portfolio_value;
        }

        $portfolioChange = $totalValue > 0 ? $weighted / $totalValue : 0.0;

        return [
            'portfolio_value' => $totalValue,
            'portfolio_value_formatted' => $this->formatLargeNumber($totalValue),
            'portfolio_change' => round($portfolioChange, 2),
            'portfolio_change_formatted' => $this->formatPercent($portfolioChange),
            'fund_count' => $funds->count(),
            'fund_count_formatted' => number_format($funds->count(), 0, ',', '.'),
            'investor_count' => (int) $funds->sum('investor_count'),
            'investor_count_formatted' => number_format((int) $funds->sum('investor_count'), 0, ',', '.'),
        ];
    }

    /**
     * İki fonu karşılaştırma satırları.
     *
     * @return array<int, array{label: string, a: string, b: string, better: string}>
     */
    public function compare(Fund $a, Fund $b, ?CarbonInterface $at = null): array
    {
        $left = $this->snapshot($a, $at);
        $right = $this->snapshot($b, $at);

        $rows = [
            ['label' => __('media.fund_price_tl'), 'a' => $left['price_formatted'], 'b' => $right['price_formatted'], 'aNum' => $left['price'], 'bNum' => $right['price'], 'higher' => true],
            ['label' => __('media.daily_return'), 'a' => $left['change_tl_formatted'], 'b' => $right['change_tl_formatted'], 'aNum' => $left['change_tl'], 'bNum' => $right['change_tl'], 'higher' => true],
        ];

        foreach (Fund::returnColumns() as $column) {
            $field = $column['field'];
            if ($field === 'return_daily') {
                continue;
            }
            $rows[] = [
                'label' => $column['label'],
                'a' => $this->formatPercent($left['returns'][$field]),
                'b' => $this->formatPercent($right['returns'][$field]),
                'aNum' => $left['returns'][$field],
                'bNum' => $right['returns'][$field],
                'higher' => true,
            ];
        }

        $rows[] = [
            'label' => __('media.risk_value'),
            'a' => $left['risk_value'].' / 7',
            'b' => $right['risk_value'].' / 7',
            'aNum' => $left['risk_value'],
            'bNum' => $right['risk_value'],
            'higher' => false,
        ];

        return $rows;
    }

    /**
     * Yüzde biçimlendirme (virgüllü, işaretli).
     */
    public function formatPercent(float $value): string
    {
        return number_format($value, 2, ',', '.').'%';
    }

    /**
     * Fon fiyatı biçimlendirme (6 ondalık, virgüllü — birebir TEFAS deseni).
     */
    public function formatPrice(float $value): string
    {
        return number_format($value, 6, ',', '.');
    }

    /**
     * Büyük değerleri Milyon/Milyar/Trilyon olarak biçimlendirir.
     */
    public function formatLargeNumber(float $value): string
    {
        if ($value >= 1_000_000_000_000) {
            return '₺'.number_format($value / 1_000_000_000_000, 2, ',', '.').' Trilyon';
        }

        if ($value >= 1_000_000_000) {
            return '₺'.number_format($value / 1_000_000_000, 2, ',', '.').' Milyar';
        }

        if ($value >= 1_000_000) {
            return '₺'.number_format($value / 1_000_000, 2, ',', '.').' Milyon';
        }

        return '₺'.number_format($value, 0, ',', '.');
    }

    private function direction(float $change): string
    {
        return $change > 0 ? 'up' : ($change < 0 ? 'down' : 'flat');
    }
}