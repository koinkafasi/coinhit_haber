<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsHomeWidgets;
use App\Http\Controllers\Concerns\BuildsMarketWidget;
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
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class HomeController extends Controller
{
    use BuildsHomeWidgets, BuildsMarketWidget;

    public function __construct(private readonly MarketDataService $market) {}

    public function index(): View
    {
        $published = fn () => News::where('status', 'published');

        $featured = (clone $published())
            ->where('is_featured', true)
            ->with(['category', 'author'])
            ->orderByDesc('published_at')
            ->take(12)
            ->get();

        $latest = (clone $published())
            ->with(['category', 'author'])
            ->orderByDesc('published_at')
            ->take(30)
            ->get();

        // Manşet slider: referanstaki 12 slaytı her zaman doldur. Önce öne
        // çıkanlar (featured), eksik kalanı en yeni yayınlarla doldur.
        $hero = $featured->take(12);
        if ($hero->count() < 12) {
            $heroIds = $hero->pluck('news_id')->all();
            $hero = $hero->merge(
                (clone $published())
                    ->whereNotIn('news_id', $heroIds)
                    ->orderByDesc('published_at')
                    ->take(12 - $hero->count())
                    ->get()
            );
        }

        return view('home.index', array_merge([
            'hero' => $hero,
            'featuredStrip' => $latest->take(8),
            'featuredCards' => $latest->slice(8, 6)->values(),
            'allNews' => $latest->take(12),
            'programs' => $this->programEpisodes(6),
            'videos' => $this->programEpisodes(8, 6),
            'broadcast' => $this->broadcastToday(),
            'galleries' => $this->cached('home.galleries', 60, fn () => Gallery::with('category')->orderByDesc('published_at')->take(6)->get()),
            'podcasts' => $this->cached('home.podcasts', 300, fn () => Podcast::where('is_active', true)->orderBy('sort_order')->take(6)->get()),
            'kapDisclosures' => $this->cached('home.kap', 120, fn () => KapDisclosure::orderByDesc('published_at')->take(12)->get()),
            'charts' => $this->cached('home.charts', 120, fn () => ChartArchive::orderByDesc('published_at')->take(4)->get()),
            'trends' => $this->cached('home.trends', 120, fn () => Trend::where('is_active', true)->with(['news' => fn ($q) => $q->where('status', 'published')->orderByDesc('published_at')->take(4)])->orderBy('sort_order')->get()),
            'sectorNews' => $this->categoryNews('sektor-haberleri', 5),
            'politicsNews' => $this->categoryNews('siyaset', 4),
            'marketNews' => $this->categoryNews('piyasalar', 5),
            'mostReadTabs' => $this->mostReadTabs(8),
            'featuredNews' => $this->featuredNews(7),
            'commodityNews' => $this->categoryNewsModule('emtia', 4),
            'marketWidget' => $this->cached('home.market_widget', 30, fn () => $this->marketWidget()),
            'marketTables' => $this->cached('home.market_tables', 30, fn () => $this->marketTableData()),
            'cryptoQuotes' => $this->market->quotes('crypto')->take(15)->values(),
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
     * `sec` değerleri HomeController.shop grafik motorunun `data-name-sec`
     * sözleşmesine karşılık gelir (foreignCurrency = Döviz, commodity = Emtia).
     *
     * @return array<int, array{key: string, label: string, sec: string, items: array<int, array{quote: Quote}>}>
     */
    private function marketWidget(): array
    {
        $tabs = [];
        $tabIndex = 0;

        foreach ($this->market->widgetGroups() as $key => $tab) {
            $items = [];
            $i = 0;

            foreach ($tab['codes'] as $code) {
                $instrument = $this->market->all()->firstWhere('code', $code);

                if (! $instrument) {
                    continue;
                }

                $items[] = [
                    'quote' => $this->market->quote($instrument),
                    'svg' => $this->market->sparklineSvg($instrument),
                    'svg_big' => $this->market->highchartsArea($instrument, 64, 520, 180, $tab['sec'].'-'.$i),
                ];
                $i++;
            }

            $tabs[] = ['key' => $key, 'label' => $tab['label'], 'sec' => $tab['sec'], 'items' => $items];
            $tabIndex++;
        }

        return $tabs;
    }
}
