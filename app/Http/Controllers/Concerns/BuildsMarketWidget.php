<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Category;
use App\Models\ChartArchive;
use App\Models\News;
use App\Models\Trend;
use App\Services\Market\MarketDataService;
use App\Services\Market\Quote;
use App\Support\Locale;
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

    /**
     * Piyasa verisi tabloları (Bölüm 7 modül 19, §15.1/§15.2): her biri kendi
     * h2 + tablo başlığıyla tam genişlik sunulur.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function marketTableData(): array
    {
        $market = app(MarketDataService::class);
        $stocks = $market->quotes('stock');

        return [
            ['key' => 'doviz', 'title' => __('ui.tables.doviz'), 'href' => Locale::url('/doviz'), 'quotes' => $market->quotes('currency')],
            ['key' => 'pariteler', 'title' => __('ui.tables.pariteler'), 'href' => Locale::url('/doviz/pariteler'), 'quotes' => $market->quotes('parity')->take(12)->values()],
            ['key' => 'gelisen-piyasa', 'title' => __('ui.tables.gelisen_piyasa'), 'href' => Locale::url('/doviz'), 'quotes' => $market->quotes('emerging_fx')],
            ['key' => 'bist', 'title' => __('ui.tables.bist'), 'href' => Locale::url('/borsa'), 'quotes' => $market->quotes('bist_index')->take(15)->values()],
            [
                'key' => 'artanlar',
                'title' => __('ui.tables.artanlar'),
                'href' => Locale::url('/borsa/en-cok'),
                'quotes' => $stocks->sortByDesc(fn ($q) => $q->changePercent)->take(10)->values(),
            ],
            [
                'key' => 'azalanlar',
                'title' => __('ui.tables.azalanlar'),
                'href' => Locale::url('/borsa/en-cok'),
                'quotes' => $stocks->sortBy(fn ($q) => $q->changePercent)->take(10)->values(),
            ],
            ['key' => 'tahvil', 'title' => __('ui.tables.tahvil'), 'href' => Locale::url('/faiz-bono'), 'quotes' => $market->quotes('bond_tr')],
            ['key' => 'repo', 'title' => __('ui.tables.repo'), 'href' => Locale::url('/faiz-bono'), 'quotes' => $market->quotes('repo')],
            ['key' => 'eurobond', 'title' => __('ui.tables.eurobond'), 'href' => Locale::url('/faiz-bono'), 'quotes' => $market->quotes('eurobond')],
            [
                'key' => 'altin',
                'title' => __('ui.tables.altin'),
                'href' => Locale::url('/altin'),
                'quotes' => $market->quotes('gold'),
                'columns' => [
                    ['label' => __('market.bid'), 'field' => 'bid', 'class' => 'lastPrice'],
                    ['label' => __('market.ask'), 'field' => 'ask', 'class' => 'lastPrice'],
                    ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
                ],
            ],
            ['key' => 'kiymetli', 'title' => __('ui.tables.kiymetli'), 'href' => Locale::url('/emtia'), 'quotes' => $market->quotes('precious_metal')],
            ['key' => 'tarim', 'title' => __('ui.tables.tarim'), 'href' => Locale::url('/emtia'), 'quotes' => $market->quotes('agriculture')],
            ['key' => 'alternatif-piyasalar', 'title' => __('ui.tables.alternatif_piyasalar'), 'href' => Locale::url('/alternatif'), 'quotes' => $market->quotes('alternative')],
            ['key' => 'alternatif', 'title' => __('ui.tables.alternatif'), 'href' => Locale::url('/alternatif'), 'quotes' => $market->quotes('baltic')],
        ];
    }
}
