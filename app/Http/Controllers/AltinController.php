<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsMarketWidget;
use App\Models\Category;
use App\Models\MarketInstrument;
use App\Models\News;
use App\Services\Market\GoldCalculator;
use App\Services\Market\MarketDataService;
use App\Services\Market\Quote;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

/**
 * Altın bölümü (spec §5, §6, §8 / §15.5) — Altın Fiyatları, Serbest Piyasada
 * Altın (17), altın çeşidi detay sayfaları, Diğer Emtia ve Altın Hesaplama.
 */
class AltinController extends Controller
{
    use BuildsMarketWidget;

    /**
     * /altin alt-hızlı bağlantıları (spec §15.5: Ons|Gram|Çeyrek|Cumhuriyet|
     * Ziynet|14 Ayar|22 Ayar|Diğer Emtia).
     *
     * @return array<int, array{label: string, url: string}>
     */
    protected function quickLinks(): array
    {
        return [
            ['label' => __('market.gold_ons'), 'url' => '/altin/altin-ons'],
            ['label' => __('market.gold_gram'), 'url' => '/altin/gram-altin'],
            ['label' => __('market.gold_ceyrek'), 'url' => '/altin/ceyrek-altin'],
            ['label' => __('market.gold_cumhuriyet'), 'url' => '/altin/cumhuriyet-altini'],
            ['label' => __('market.gold_ziynet'), 'url' => '/altin/ziynet-altini'],
            ['label' => __('market.gold_14_ayar'), 'url' => '/altin/14-ay-bilezik-gr'],
            ['label' => __('market.gold_22_ayar'), 'url' => '/altin/22-ay-bilezik-gr'],
            ['label' => __('market.other_commodities'), 'url' => '/emtia'],
        ];
    }

    /**
     * Serbest Piyasada Altın tablosu — 17 satır birebir sıra (spec §5, §8).
     *
     * @var array<int, string>
     */
    public const SERBEST_CODES = [
        'GAU', 'GRAM', 'CUMHUR', 'TAM', 'ZIYNET', 'CEYREK', 'YARIM', 'ATA',
        'BILEZIK14', 'BILEZIK22', 'GREMSE', 'GAUTRY', 'HAS', 'IKIBUCUK', 'BESLI',
        'KULCE_EUR', 'KULCE_USD',
    ];

    /**
     * Altın Fiyatları hero tablosu (spec §8: GRAM ALTIN, ÇEYREK ALTIN, ALTIN/ONS).
     *
     * @var array<int, string>
     */
    private const FEATURE_CODES = ['GRAM', 'CEYREK', 'GAU'];

    public function __construct(
        private readonly MarketDataService $market,
        private readonly GoldCalculator $calculator,
    ) {}

    /**
     * /altin — Altın Fiyatları + Serbest Piyasada Altın + Piyasalar + Diğer Emtia.
     */
    public function index(): View
    {
        return view('altin.index', array_merge([
            'quickLinks' => $this->quickLinks(),
            'featureQuotes' => $this->quotesFor(self::FEATURE_CODES),
            'serbestQuotes' => $this->serbestQuotes(),
            'otherCommodity' => $this->market->quotes(MarketInstrument::GROUP_PRECIOUS_METAL),
            'relatedNews' => $this->categoryNews('piyasalar', 4),
            'calculator' => $this->calculator->defaultContext(),
            'cmeNote' => $this->market->sourceNote('cme'),
        ], $this->marketWidgetData(), $this->layoutData()));
    }

    /**
     * /altin/{slug} — altın çeşidi detay (hero + güniçi + serbest + hesaplama).
     */
    public function detail(string $slug): View
    {
        $instrument = $this->resolveInstrument($slug);

        abort_unless($instrument, 404);

        $quote = $this->market->quote($instrument);

        return view('altin.detail', array_merge([
            'quickLinks' => $this->quickLinks(),
            'instrument' => $instrument,
            'quote' => $quote,
            'breadcrumb' => [
                ['label' => __('market.gold'), 'url' => \App\Support\Locale::url('/altin')],
                ['label' => $instrument->name],
            ],
            'chart' => $this->market->sparklineSvg($instrument, 90, 900, 300),
            'ranges' => ['1G', '1H', __('market.all_range')],
            'serbestQuotes' => $this->serbestQuotes(),
            'relatedNews' => $this->categoryNews('piyasalar', 4),
            'featured' => $this->featured(4),
            'calculator' => $this->calculator->defaultContext(),
            'cmeNote' => $this->market->sourceNote('cme'),
        ], $this->layoutData()));
    }

    /**
     * /altin-cevirici — bağımsız Altın Hesaplama araç sayfası (spec §3).
     */
    public function calculator(): View
    {
        return view('altin.calculator', array_merge([
            'quickLinks' => $this->quickLinks(),
            'calculator' => $this->calculator->defaultContext(),
            'relatedNews' => $this->categoryNews('piyasalar', 4),
            'featured' => $this->featured(4),
        ], $this->layoutData()));
    }

    /**
     * Detay rotası slug'ını altın enstrümanına çözer (okunur slug veya takma ad).
     */
    private function resolveInstrument(string $slug): ?MarketInstrument
    {
        $code = array_search($slug, MarketInstrument::GOLD_ROUTE_SLUGS, true);

        return MarketInstrument::where('group', MarketInstrument::GROUP_GOLD)
            ->when($code !== false, fn ($query) => $query->where('code', $code))
            ->when($code === false, fn ($query) => $query->where('slug', $slug))
            ->first();
    }

    /**
     * @return Collection<int, Quote>
     */
    private function serbestQuotes(): Collection
    {
        return $this->quotesFor(self::SERBEST_CODES);
    }

    /**
     * @param  array<int, string>  $codes
     * @return Collection<int, Quote>
     */
    private function quotesFor(array $codes): Collection
    {
        return collect($codes)
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
