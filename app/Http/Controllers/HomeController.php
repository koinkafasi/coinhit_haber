<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsHomeWidgets;
use App\Models\BreakingNews;
use App\Models\BroadcastSchedule;
use App\Models\Category;
use App\Models\ChartArchive;
use App\Models\Gallery;
use App\Models\KapDisclosure;
use App\Models\News;
use App\Models\Podcast;
use App\Models\ProgramEpisode;
use App\Models\Trend;
use App\Services\Market\MarketDataService;
use App\Services\Market\Quote;
use App\Support\Locale;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    use BuildsHomeWidgets;

    public function __construct(private readonly MarketDataService $market) {}

    public function index(): View
    {
        $published = fn () => News::where('status', 'published');

        $featured = (clone $published())
            ->where('is_featured', true)
            ->with(['category', 'author'])
            ->orderByDesc('published_at')
            ->take(6)
            ->get();

        $latest = (clone $published())
            ->with(['category', 'author'])
            ->orderByDesc('published_at')
            ->take(30)
            ->get();

        $hero = $featured->isNotEmpty() ? $featured : $latest->take(6);

        return view('home.index', array_merge([
            'hero' => $hero,
            'featuredStrip' => $latest->take(8),
            'featuredCards' => $latest->slice(8, 6)->values(),
            'allNews' => $latest->take(12),
            'programs' => $this->programEpisodes(6),
            'videos' => $this->programEpisodes(8, 6),
            'broadcast' => $this->broadcastToday(),
            'galleries' => Gallery::with('category')->orderByDesc('published_at')->take(6)->get(),
            'podcasts' => Podcast::where('is_active', true)->orderBy('sort_order')->take(6)->get(),
            'kapDisclosures' => KapDisclosure::orderByDesc('published_at')->take(8)->get(),
            'charts' => ChartArchive::orderByDesc('published_at')->take(4)->get(),
            'trends' => Trend::where('is_active', true)->with(['news' => fn ($q) => $q->where('status', 'published')->orderByDesc('published_at')->take(4)])->orderBy('sort_order')->get(),
            'sectorNews' => $this->categoryNews('sektor-haberleri', 5),
            'politicsNews' => $this->categoryNews('siyaset', 4),
            'marketNews' => $this->categoryNews('piyasalar', 5),
            'mostReadTabs' => $this->mostReadTabs(8),
            'featuredNews' => $this->featuredNews(6),
            'commodityNews' => $this->categoryNewsModule('emtia', 4),
            'marketWidget' => $this->marketWidget(),
            'marketTables' => $this->marketTables(),
            'cryptoQuotes' => $this->market->quotes('crypto')->take(12)->values(),
            'latestBreaking' => BreakingNews::where('is_active', true)->orderByDesc('published_at')->take(10)->get(),
        ], $this->layoutData()));
    }

    /**
     * Öne çıkan program bölümleri (Bölüm 7 modül 7 / 15).
     *
     * @return Collection<int, ProgramEpisode>
     */
    private function programEpisodes(int $take, int $skip = 0): Collection
    {
        return ProgramEpisode::with('program')
            ->orderByDesc('air_at')
            ->skip($skip)
            ->take($take)
            ->get();
    }

    /**
     * Bugünün yayın akışı (Bölüm 7 modül 11).
     *
     * @return Collection<int, BroadcastSchedule>
     */
    private function broadcastToday(): Collection
    {
        $day = (int) CarbonImmutable::now()->isoWeekday();

        $schedule = BroadcastSchedule::where('day_of_week', $day)
            ->orderBy('air_time')
            ->get();

        if ($schedule->isEmpty()) {
            $schedule = BroadcastSchedule::orderBy('day_of_week')->orderBy('air_time')->take(12)->get();
        }

        return $schedule;
    }

    /**
     * @return Collection<int, News>
     */
    private function categoryNews(string $slug, int $take): Collection
    {
        $category = Category::where('slug', $slug)->first();

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
     * PİYASALAR büyük widget: 4 sekme + enstrüman alt-sekmeleri + mini grafikler.
     *
     * @return array<int, array{key: string, label: string, items: array<int, array{quote: Quote, svg: string}>}>
     */
    private function marketWidget(): array
    {
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
                $instrument = $this->market->all()->firstWhere('code', $code);

                if (! $instrument) {
                    continue;
                }

                $items[] = [
                    'quote' => $this->market->quote($instrument),
                    'svg' => $this->market->sparklineSvg($instrument),
                ];
            }

            $tabs[] = ['key' => $key, 'label' => $tab['label'], 'items' => $items];
        }

        return $tabs;
    }

    /**
     * Ana sayfa piyasa verisi tabloları (Bölüm 7 modül 19, §15.1).
     *
     * @return array<int, array<string, mixed>>
     */
    private function marketTables(): array
    {
        $stocks = $this->market->quotes('stock');

        return [
            ['key' => 'doviz', 'title' => __('ui.tables.doviz'), 'href' => Locale::url('/doviz'), 'quotes' => $this->market->quotes('currency')],
            ['key' => 'pariteler', 'title' => __('ui.tables.pariteler'), 'href' => Locale::url('/doviz/pariteler'), 'quotes' => $this->market->quotes('parity')->take(12)->values()],
            ['key' => 'gelisen-piyasa', 'title' => __('ui.tables.gelisen_piyasa'), 'href' => Locale::url('/doviz'), 'quotes' => $this->market->quotes('emerging_fx')],
            ['key' => 'bist', 'title' => __('ui.tables.bist'), 'href' => Locale::url('/borsa'), 'quotes' => $this->market->quotes('bist_index')->take(15)->values()],
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
            ['key' => 'tahvil', 'title' => __('ui.tables.tahvil'), 'href' => Locale::url('/faiz-bono'), 'quotes' => $this->market->quotes('bond_tr')],
            ['key' => 'repo', 'title' => __('ui.tables.repo'), 'href' => Locale::url('/faiz-bono'), 'quotes' => $this->market->quotes('repo')],
            ['key' => 'eurobond', 'title' => __('ui.tables.eurobond'), 'href' => Locale::url('/faiz-bono'), 'quotes' => $this->market->quotes('eurobond')],
            [
                'key' => 'altin',
                'title' => __('ui.tables.altin'),
                'href' => Locale::url('/altin'),
                'quotes' => $this->market->quotes('gold'),
                'columns' => [
                    ['label' => 'ALIŞ', 'field' => 'bid', 'class' => 'lastPrice'],
                    ['label' => 'SATIŞ', 'field' => 'ask', 'class' => 'lastPrice'],
                    ['label' => '%', 'field' => 'change_percent', 'class' => 'percentChange', 'color' => true],
                ],
            ],
            ['key' => 'kiymetli', 'title' => __('ui.tables.kiymetli'), 'href' => Locale::url('/emtia'), 'quotes' => $this->market->quotes('precious_metal')],
            ['key' => 'tarim', 'title' => __('ui.tables.tarim'), 'href' => Locale::url('/emtia'), 'quotes' => $this->market->quotes('agriculture')],
            ['key' => 'alternatif-piyasalar', 'title' => __('ui.tables.alternatif_piyasalar'), 'href' => Locale::url('/alternatif'), 'quotes' => $this->market->quotes('alternative')],
            ['key' => 'alternatif', 'title' => __('ui.tables.alternatif'), 'href' => Locale::url('/alternatif'), 'quotes' => $this->market->quotes('baltic')],
        ];
    }
}
