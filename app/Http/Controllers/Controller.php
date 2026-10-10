<?php

namespace App\Http\Controllers;

use App\Models\BreakingNews;
use App\Models\Category;
use App\Models\SiteSetting;
use App\Services\Market\MarketDataService;
use App\Services\Market\Quote;
use Illuminate\Support\Collection;

abstract class Controller
{
    /**
     * Tüm Blade görünümleri için ortak çerçeve verisi.
     *
     * @return array<string, mixed>
     */
    protected function layoutData(): array
    {
        return [
            'navCategories' => Category::with('children')
                ->whereNull('parent_id')
                ->orderBy('sort_order')
                ->get(),
            'breakingList' => BreakingNews::where('is_active', true)
                ->orderByDesc('published_at')
                ->take(10)
                ->get(),
            'financeGroups' => $this->financeSliderGroups(),
            'siteSettings' => SiteSetting::all()
                ->mapWithKeys(fn (SiteSetting $setting) => [$setting->key => $setting->value])
                ->all(),
        ];
    }

    /**
     * Finance-slider / piyasa ticker sekmeleri (Bölüm 2.4, 17.3).
     *
     * @return array<string, array{label: string, quotes: Collection<int, Quote>}>
     */
    protected function financeSliderGroups(): array
    {
        $definitions = [
            'borsa' => ['label' => __('ui.market.borsa'), 'codes' => ['XU100', 'DOW', 'DAX', 'FTSE100', 'SHANGHAI']],
            'doviz' => ['label' => __('ui.market.doviz'), 'codes' => ['USDTRY', 'EURTRY', 'GBPTRY', 'EURUSD', 'DXY']],
            'emtia' => ['label' => __('ui.market.emtia'), 'codes' => ['GAU', 'GRAM', 'GUMUS_ONS', 'BRENT', 'WTI', 'BDIY']],
            'faiz' => ['label' => __('ui.market.faiz'), 'codes' => ['TR2Y', 'TR10Y', 'US10Y', 'DE10Y', 'GB10Y']],
        ];

        $market = app(MarketDataService::class);
        $groups = [];

        foreach ($definitions as $key => $definition) {
            $quotes = collect($definition['codes'])
                ->map(function (string $code) use ($market) {
                    $instrument = $market->all()->firstWhere('code', $code);

                    return $instrument ? $market->quote($instrument) : null;
                })
                ->filter()
                ->values();

            $groups[$key] = ['label' => $definition['label'], 'quotes' => $quotes];
        }

        return $groups;
    }
}
