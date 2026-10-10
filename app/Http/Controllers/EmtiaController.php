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
 * Emtia bölümü (spec §5, §8 / §15.5 "Diğer Emtia") — Kıymetli Metaller,
 * Tarım Emtia, Sanayi Emtia (+3 Aylık), Enerji Emtia ve Serbest Piyasada
 * Altın listesi.
 */
class EmtiaController extends Controller
{
    use BuildsMarketWidget;

    /**
     * /emtia alt-hızlı bağlantıları (özet tablolara çapa).
     *
     * @var array<int, array{label: string, url: string}>
     */
    public const QUICK_LINKS = [
        ['label' => 'Kıymetli Metaller', 'url' => '/emtia#kiymetli-metaller'],
        ['label' => 'Tarım Emtia', 'url' => '/emtia#tarim-emtia'],
        ['label' => 'Sanayi Emtia', 'url' => '/emtia#sanayi-emtia'],
        ['label' => 'Sanayi Emtia 3 Aylık', 'url' => '/emtia#sanayi-emtia-3-aylik'],
        ['label' => 'Enerji Emtia', 'url' => '/emtia#enerji-emtia'],
        ['label' => 'Serbest Piyasada Altın', 'url' => '/altin'],
    ];

    public function __construct(private readonly MarketDataService $market) {}

    /**
     * /emtia — Kıymetli/Tarım/Sanayi(+3 Aylık)/Enerji tabloları + altın listesi.
     */
    public function index(): View
    {
        $industry = $this->market->quotes(MarketInstrument::GROUP_INDUSTRY);

        return view('emtia.index', array_merge([
            'quickLinks' => self::QUICK_LINKS,
            'preciousMetals' => $this->market->quotes(MarketInstrument::GROUP_PRECIOUS_METAL),
            'agriculture' => $this->market->quotes(MarketInstrument::GROUP_AGRICULTURE),
            'industry' => $industry->reject(fn (Quote $q) => self::isForward($q->code()))->values(),
            'industryForward' => $industry->filter(fn (Quote $q) => self::isForward($q->code()))->values(),
            'energy' => $this->market->quotes(MarketInstrument::GROUP_ENERGY),
            'serbestQuotes' => $this->serbestQuotes(),
            'relatedNews' => $this->categoryNews('piyasalar', 4),
            'cmeNote' => $this->market->sourceNote('cme'),
        ], $this->marketWidgetData(), $this->layoutData()));
    }

    /**
     * /emtia/{slug} — emtia/Baltık detay (3 aylık vadeli, brent, bdiy ...).
     */
    public function detail(string $slug): View
    {
        $instrument = $this->resolveInstrument($slug);

        abort_unless($instrument, 404);

        $quote = $this->market->quote($instrument);

        return view('emtia.detail', array_merge([
            'instrument' => $instrument,
            'quote' => $quote,
            'breadcrumb' => [
                ['label' => 'Emtia', 'url' => '/emtia'],
                ['label' => $instrument->name],
            ],
            'chart' => $this->market->sparklineSvg($instrument, 90, 900, 300),
            'ranges' => ['1G', '1H', 'Tümü'],
            'relatedNews' => $this->categoryNews('piyasalar', 4),
            'cmeNote' => $this->market->sourceNote('cme'),
        ], $this->layoutData()));
    }

    /**
     * Emtia/Baltık detay slug'ını çözer (takma adlar dahil).
     */
    private function resolveInstrument(string $slug): ?MarketInstrument
    {
        $groups = [
            MarketInstrument::GROUP_PRECIOUS_METAL,
            MarketInstrument::GROUP_AGRICULTURE,
            MarketInstrument::GROUP_INDUSTRY,
            MarketInstrument::GROUP_ENERGY,
            MarketInstrument::GROUP_BALTIC,
        ];

        $alias = MarketInstrument::COMMODITY_ROUTE_ALIASES[$slug] ?? null;
        $query = MarketInstrument::whereIn('group', $groups);

        if ($alias !== null) {
            return $query->where('code', $alias)->first();
        }

        $code = array_search($slug, MarketInstrument::COMMODITY_ROUTE_SLUGS, true);

        return ($code !== false ? $query->where('code', $code) : $query->where('slug', $slug))->first();
    }

    private static function isForward(string $code): bool
    {
        return str_ends_with($code, '3A');
    }

    /**
     * @return Collection<int, Quote>
     */
    private function serbestQuotes(): Collection
    {
        return collect(AltinController::SERBEST_CODES)
            ->map(fn (string $code) => $this->market->all()->firstWhere('code', $code))
            ->filter()
            ->map(fn (MarketInstrument $instrument) => $this->market->quote($instrument))
            ->values();
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
}
