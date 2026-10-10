<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsMarketWidget;
use App\Models\Category;
use App\Models\MarketInstrument;
use App\Models\News;
use App\Services\Market\MarketDataService;
use App\Services\Market\Quote;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

/**
 * Faiz-Bono bölümü (spec §5, §8 / §15.7) — Tahvil (TR), Repo, Eurobondlar,
 * Uluslararası Tahviller ve detay sayfaları.
 */
class FaizBonoController extends Controller
{
    use BuildsMarketWidget;

    public function __construct(private readonly MarketDataService $market) {}

    /**
     * /faiz-bono — Tahvil + Repo + Eurobondlar + Uluslararası Tahviller + haberler.
     */
    public function index(string $pageTitle = 'Faiz Bono'): View
    {
        return view('faiz-bono.index', array_merge([
            'pageTitle' => $pageTitle,
            'bondTr' => $this->market->quotes(MarketInstrument::GROUP_BOND_TR),
            'repo' => $this->market->quotes(MarketInstrument::GROUP_REPO),
            'eurobonds' => $this->market->quotes(MarketInstrument::GROUP_EUROBOND),
            'intlBonds' => $this->market->quotes(MarketInstrument::GROUP_INTL_BOND),
            'relatedNews' => $this->categoryNews('piyasalar', 4),
            'note' => $this->market->sourceNote('foreks'),
        ], $this->marketWidgetData(), $this->layoutData()));
    }

    /**
     * /tahvil/faiz — genel tahvil/faiz görünümü (spec §3).
     */
    public function tahvilFaiz(): View
    {
        return $this->index('Tahvil');
    }

    /**
     * /tahvil/{slug} — TR tahvil veya uluslararası tahvil detay sayfası.
     */
    public function bondDetail(string $slug): View
    {
        $instrument = $this->resolveBond($slug);

        abort_unless($instrument, 404);

        return view('faiz-bono.detail', array_merge($this->detailData($instrument), [
            'groupTitle' => 'Tahvil',
            'groupQuotes' => $this->market->quotes($instrument->group),
            'urlResolver' => fn (Quote $q) => $q->instrument->bondUrl(),
            'breadcrumb' => [
                ['label' => 'Faiz Bono', 'url' => '/faiz-bono'],
                ['label' => $instrument->name],
            ],
        ], $this->layoutData()));
    }

    /**
     * /repo/{slug} — repo detay sayfası.
     */
    public function repoDetail(string $slug): View
    {
        $code = array_search($slug, MarketInstrument::REPO_ROUTE_SLUGS, true);
        $instrument = MarketInstrument::where('group', MarketInstrument::GROUP_REPO)
            ->when($code !== false, fn ($query) => $query->where('code', $code))
            ->when($code === false, fn ($query) => $query->where('slug', $slug))
            ->first();

        abort_unless($instrument, 404);

        return view('faiz-bono.detail', array_merge($this->detailData($instrument), [
            'groupTitle' => 'Repo',
            'groupQuotes' => $this->market->quotes(MarketInstrument::GROUP_REPO),
            'urlResolver' => fn (Quote $q) => $q->instrument->repoUrl(),
            'breadcrumb' => [
                ['label' => 'Faiz Bono', 'url' => '/faiz-bono'],
                ['label' => $instrument->name],
            ],
        ], $this->layoutData()));
    }

    /**
     * /eurobond/{slug} — eurobond detay sayfası.
     */
    public function eurobondDetail(string $slug): View
    {
        $code = array_search($slug, MarketInstrument::EUROBOND_ROUTE_SLUGS, true);
        $instrument = MarketInstrument::where('group', MarketInstrument::GROUP_EUROBOND)
            ->when($code !== false, fn ($query) => $query->where('code', $code))
            ->when($code === false, fn ($query) => $query->where('slug', $slug))
            ->first();

        abort_unless($instrument, 404);

        return view('faiz-bono.detail', array_merge($this->detailData($instrument), [
            'groupTitle' => 'Eurobondlar',
            'groupQuotes' => $this->market->quotes(MarketInstrument::GROUP_EUROBOND),
            'urlResolver' => fn (Quote $q) => $q->instrument->eurobondUrl(),
            'breadcrumb' => [
                ['label' => 'Faiz Bono', 'url' => '/faiz-bono'],
                ['label' => $instrument->name],
            ],
        ], $this->layoutData()));
    }

    /**
     * Detay görünümü ortak verisi.
     *
     * @return array<string, mixed>
     */
    private function detailData(MarketInstrument $instrument): array
    {
        $quote = $this->market->quote($instrument);

        return [
            'instrument' => $instrument,
            'quote' => $quote,
            'chart' => $this->market->sparklineSvg($instrument, 90, 900, 300),
            'ranges' => ['1G', '1H', 'Tümü'],
            'relatedNews' => $this->categoryNews('piyasalar', 4),
            'featured' => $this->featured(4),
            'note' => $this->market->sourceNote('foreks'),
        ];
    }

    /**
     * Tahvil slug'ını (TR + uluslararası + `almayna` typo takma adı) çözer.
     */
    private function resolveBond(string $slug): ?MarketInstrument
    {
        $aliasCode = MarketInstrument::INTL_BOND_ROUTE_ALIASES[$slug] ?? null;

        if ($aliasCode !== null) {
            return MarketInstrument::where('group', MarketInstrument::GROUP_INTL_BOND)
                ->where('code', $aliasCode)
                ->first();
        }

        $code = array_search($slug, MarketInstrument::BOND_TR_ROUTE_SLUGS, true);
        if ($code !== false) {
            return MarketInstrument::where('group', MarketInstrument::GROUP_BOND_TR)
                ->where('code', $code)
                ->first();
        }

        $code = array_search($slug, MarketInstrument::INTL_BOND_ROUTE_SLUGS, true);

        return MarketInstrument::where('group', MarketInstrument::GROUP_INTL_BOND)
            ->when($code !== false, fn ($query) => $query->where('code', $code))
            ->when($code === false, fn ($query) => $query->where('slug', $slug))
            ->first();
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
     * @return Collection<int, News>
     */
    private function featured(int $take): Collection
    {
        $news = News::where('status', 'published')
            ->where('category_id', Category::where('slug', 'piyasalar')->value('id'))
            ->orderByDesc('published_at')
            ->take($take)
            ->get();

        if ($news->isEmpty()) {
            $news = News::where('status', 'published')->orderByDesc('published_at')->take($take)->get();
        }

        return $news;
    }
}
