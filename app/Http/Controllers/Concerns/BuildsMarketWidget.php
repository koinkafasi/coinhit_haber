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

        $tabs = [];
        $tabIndex = 0;

        foreach ($market->widgetGroups() as $key => $tab) {
            $items = [];
            $i = 0;

            foreach ($tab['codes'] as $code) {
                $instrument = $market->all()->firstWhere('code', $code);

                if (! $instrument) {
                    continue;
                }

                $items[] = [
                    'quote' => $market->quote($instrument),
                    'svg' => $market->sparklineSvg($instrument),
                    'svg_big' => $market->highchartsArea($instrument, 64, 414, 201, $tab['sec'].'-'.$i),
                ];
                $i++;
            }

            $tabs[] = ['key' => $key, 'label' => $tab['label'], 'sec' => $tab['sec'], 'items' => $items];
            $tabIndex++;
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
     * Piyasa verisi tabloları — referans bloomberght.com anasayfa
     * `marketDataLists` widget'ı ile birebir (Döviz/Borsa/Faiz-Bono/
     * Altın-Emtia/Alternatif sekmeleri, 14 alt tablo).
     *
     * Her tablo için referans meta verileri:
     *   - `firstCol`: ilk sütun başlığı (örn. ALTIN tablosu "Altın", Baltık "BALTIK")
     *   - `footerSmall`: altbilgi küçük etiketi ("Döviz Listesi") — `__('ui.all_see')` ile birlikte
     *   - `footerHref`: altbilgi TÜMÜNÜ GÖR bağlantısı (varsayılan: `href`)
     *   - `variant`: 'big' (yalnızca Döviz — text-base ad + cursor-pointer) / 'compact'
     *   - `columns`: SON/% varsayılanı; Altın ALIŞ/SATIŞ/%/SAAT
     *
     * @return array<int, array<string, mixed>>
     */
    protected function marketTableData(): array
    {
        $market = app(MarketDataService::class);
        $stocks = $market->quotes('stock');

        // Varsayılan sütunlar → SON + %
        $table = function (array $c) {
            return array_merge([
                'columns' => [
                    ['label' => __('market.last'), 'field' => 'last', 'class' => 'lastPrice', 'color' => true],
                    ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
                ],
            ], $c);
        };

        return [
            $table([
                'key' => 'doviz',
                'title' => __('ui.tables.doviz'),
                'firstCol' => __('ui.table_first_col.doviz'),
                'footerSmall' => __('ui.table_list_label.doviz'),
                'href' => Locale::url('/doviz'),
                'footerHref' => Locale::url('/doviz'),
                'quotes' => $market->quotes('currency'),
                'variant' => 'big',
            ]),
            $table([
                'key' => 'pariteler',
                'title' => __('ui.tables.pariteler'),
                'firstCol' => __('ui.table_first_col.pariteler'),
                'footerSmall' => __('ui.table_list_label.pariteler'),
                'href' => Locale::url('/doviz/pariteler'),
                'footerHref' => Locale::url('/doviz/pariteler'),
                'quotes' => $market->quotes('parity')->take(12)->values(),
            ]),
            $table([
                'key' => 'gelisen-piyasa',
                'title' => __('ui.tables.gelisen_piyasa'),
                'firstCol' => __('ui.table_first_col.gelisen_piyasa'),
                'footerSmall' => __('ui.table_list_label.gelisen_piyasa'),
                'href' => Locale::url('/doviz/pariteler'),
                'footerHref' => Locale::url('/doviz/pariteler'),
                'quotes' => $market->quotes('emerging_fx'),
            ]),
            $table([
                'key' => 'bist',
                'title' => __('ui.tables.bist'),
                'firstCol' => __('ui.table_first_col.bist'),
                'footerSmall' => __('ui.table_list_label.bist'),
                'href' => Locale::url('/borsa/endeksler'),
                'footerHref' => Locale::url('/borsa/endeksler'),
                'quotes' => $market->quotes('bist_index')->take(15)->values(),
            ]),
            $table([
                'key' => 'artanlar',
                'title' => __('ui.tables.artanlar'),
                'firstCol' => __('ui.table_first_col.artanlar'),
                'footerSmall' => __('ui.table_list_label.artanlar'),
                'href' => Locale::url('/borsa/en-cok'),
                'footerHref' => Locale::url('/borsa/en-cok'),
                'quotes' => $stocks->sortByDesc(fn ($q) => $q->changePercent)->take(10)->values(),
            ]),
            $table([
                'key' => 'azalanlar',
                'title' => __('ui.tables.azalanlar'),
                'firstCol' => __('ui.table_first_col.azalanlar'),
                'footerSmall' => __('ui.table_list_label.azalanlar'),
                'href' => Locale::url('/borsa/en-cok'),
                'footerHref' => Locale::url('/borsa/en-cok'),
                'quotes' => $stocks->sortBy(fn ($q) => $q->changePercent)->take(10)->values(),
            ]),
            $table([
                'key' => 'tahvil',
                'title' => __('ui.tables.tahvil'),
                'firstCol' => __('ui.table_first_col.tahvil'),
                'footerSmall' => __('ui.table_list_label.tahvil'),
                'href' => Locale::url('/faiz-bono'),
                'footerHref' => Locale::url('/faiz-bono'),
                'quotes' => $market->quotes('bond_tr'),
            ]),
            $table([
                'key' => 'repo',
                'title' => __('ui.tables.repo'),
                'firstCol' => __('ui.table_first_col.repo'),
                'footerSmall' => __('ui.table_list_label.repo'),
                'href' => Locale::url('/faiz-bono'),
                'footerHref' => Locale::url('/faiz-bono'),
                'quotes' => $market->quotes('repo'),
            ]),
            $table([
                'key' => 'eurobond',
                'title' => __('ui.tables.eurobond'),
                'firstCol' => __('ui.table_first_col.eurobond'),
                'footerSmall' => __('ui.table_list_label.eurobond'),
                'href' => Locale::url('/faiz-bono'),
                'footerHref' => Locale::url('/faiz-bono'),
                'quotes' => $market->quotes('eurobond'),
            ]),
            $table([
                'key' => 'altin',
                'title' => __('ui.tables.altin'),
                'firstCol' => __('ui.table_first_col.altin'),
                'footerSmall' => __('ui.table_list_label.altin'),
                'href' => Locale::url('/altin'),
                'footerHref' => Locale::url('/altin'),
                'quotes' => $market->quotes('gold'),
                'columns' => [
                    ['label' => __('market.bid'), 'field' => 'bid', 'class' => 'bid', 'color' => true],
                    ['label' => __('market.ask'), 'field' => 'ask', 'class' => 'ask', 'color' => true],
                    ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
                    ['label' => __('market.time'), 'field' => 'time', 'class' => '', 'color' => false],
                ],
            ]),
            $table([
                'key' => 'kiymetli',
                'title' => __('ui.tables.kiymetli'),
                'firstCol' => __('ui.table_first_col.kiymetli'),
                'footerSmall' => __('ui.table_list_label.kiymetli'),
                'href' => Locale::url('/emtia'),
                'footerHref' => Locale::url('/emtia'),
                'quotes' => $market->quotes('precious_metal'),
            ]),
            $table([
                'key' => 'tarim',
                'title' => __('ui.tables.tarim'),
                'firstCol' => __('ui.table_first_col.tarim'),
                'footerSmall' => __('ui.table_list_label.tarim'),
                'href' => Locale::url('/emtia'),
                'footerHref' => Locale::url('/emtia'),
                'quotes' => $market->quotes('agriculture'),
            ]),
            $table([
                'key' => 'alternatif-piyasalar',
                'title' => __('ui.tables.alternatif_piyasalar'),
                'firstCol' => __('ui.table_first_col.alternatif_piyasalar'),
                'footerSmall' => __('ui.table_list_label.alternatif_piyasalar'),
                'href' => Locale::url('/emtia/bdiy'),
                'footerHref' => Locale::url('/emtia/bdiy'),
                'quotes' => $market->quotes('baltic'),
            ]),
            $table([
                'key' => 'alternatif',
                'title' => __('ui.tables.alternatif'),
                'firstCol' => __('ui.table_first_col.alternatif'),
                'footerSmall' => __('ui.table_list_label.alternatif'),
                'href' => Locale::url('/alternatif'),
                'footerHref' => Locale::url('/alternatif'),
                'quotes' => $market->quotes('alternative'),
            ]),
        ];
    }
}
