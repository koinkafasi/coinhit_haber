<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Category;
use App\Models\ChartArchive;
use App\Models\News;
use App\Models\Trend;
use App\Services\Market\MarketDataService;
use App\Services\Market\Quote;
use Illuminate\Support\Collection;

/**
 * PİYASALAR büyük widget'ı (4 sekme + alt-sekmeler + haber/hızlı link/grafik)
 * için ortak veri üreticisi (spec §7 modül 7, §15).
 */
trait BuildsMarketWidget
{
    /**
     * @return array<int, array{key: string, label: string, items: array<int, array{quote: Quote, svg: string}>}>
     */
    protected function marketGraphicTabs(): array
    {
        $market = app(MarketDataService::class);

        $definition = [
            'borsa' => ['label' => __('ui.market.borsa'), 'codes' => ['XU100', 'DOW', 'DAX', 'FTSE100', 'SHANGHAI']],
            'doviz' => ['label' => __('ui.market.doviz'), 'codes' => ['USDTRY', 'EURTRY', 'GBPTRY', 'EURUSD', 'DXY']],
            'emtia' => ['label' => __('ui.market.emtia'), 'codes' => ['GAU', 'GRAM', 'GUMUS_ONS', 'BRENT', 'WTI', 'BDIY']],
            'faiz' => ['label' => __('ui.market.faiz'), 'codes' => ['TR2Y', 'TR10Y', 'US10Y', 'DE10Y', 'GB10Y']],
        ];

        $tabs = [];

        foreach ($definition as $key => $tab) {
            $items = [];

            foreach ($tab['codes'] as $code) {
                $instrument = $market->all()->firstWhere('code', $code);

                if (! $instrument) {
                    continue;
                }

                $items[] = [
                    'quote' => $market->quote($instrument),
                    'svg' => $market->sparklineSvg($instrument),
                ];
            }

            $tabs[] = ['key' => $key, 'label' => $tab['label'], 'items' => $items];
        }

        return $tabs;
    }

    /**
     * `home.partials.market-widget` görünümü için tam bağlam.
     *
     * @return array<string, mixed>
     */
    protected function marketWidgetData(): array
    {
        return [
            'marketWidget' => $this->marketGraphicTabs(),
            'marketNews' => $this->widgetMarketNews(5),
            'trends' => Trend::where('is_active', true)->orderBy('sort_order')->take(6)->get(),
            'charts' => ChartArchive::orderByDesc('published_at')->take(4)->get(),
        ];
    }

    /**
     * @return Collection<int, News>
     */
    protected function widgetMarketNews(int $take): Collection
    {
        $category = Category::where('slug', 'piyasalar')->first();

        if (! $category) {
            return collect();
        }

        return News::where('status', 'published')
            ->where('category_id', $category->id)
            ->orderByDesc('published_at')
            ->take($take)
            ->get();
    }
}
