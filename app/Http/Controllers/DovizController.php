<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\KapDisclosure;
use App\Models\MarketInstrument;
use App\Models\News;
use App\Services\Market\CurrencyConverter;
use App\Services\Market\MarketDataService;
use App\Services\Market\Quote;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

/**
 * Döviz & Parite bölümü (spec §5, §6, §8 / §15.4) — döviz kurları, pariteler,
 * gelişen piyasa kurları, kur detay sayfaları ve döviz çevirici.
 */
class DovizController extends Controller
{
    /**
     * /doviz alt-hızlı bağlantıları (spec §15.4: Dolar|Euro|Sterlin|Frank|Pariteler).
     *
     * @var array<int, array{label: string, url: string}>
     */
    private const QUICK_LINKS = [
        ['label' => 'Dolar', 'url' => '/doviz/dolar'],
        ['label' => 'Euro', 'url' => '/doviz/euro'],
        ['label' => 'Sterlin', 'url' => '/doviz/ingiliz-sterlini'],
        ['label' => 'Frank', 'url' => '/doviz/isvicre-frangi'],
        ['label' => 'Pariteler', 'url' => '/doviz/pariteler'],
    ];

    /**
     * /doviz Pariteler tablosu: 6 parite + DOLAR ENDEKSİ (spec §8).
     *
     * @var array<int, string>
     */
    private const PARITY_CODES = ['EURUSD', 'USDJPY', 'GBPUSD', 'EURJPY', 'USDCHF', 'EURGBP', 'DXY'];

    /**
     * Mini quote kartları (spec §8 / §15.4: USD/TRY, EUR/TRY, EUR/USD).
     *
     * @var array<int, string>
     */
    private const MINI_CARD_CODES = ['USDTRY', 'EURTRY', 'EURUSD'];

    public function __construct(
        private readonly MarketDataService $market,
        private readonly CurrencyConverter $converter,
    ) {}

    /**
     * /doviz — Döviz Kurları + Pariteler + Gelişen Piyasa Kurları + haberler.
     */
    public function index(): View
    {
        return view('doviz.index', array_merge([
            'quickLinks' => self::QUICK_LINKS,
            'currencyQuotes' => $this->market->quotes(MarketInstrument::GROUP_CURRENCY),
            'parityQuotes' => $this->parityQuotes(),
            'emergingQuotes' => $this->market->quotes(MarketInstrument::GROUP_EMERGING_FX),
            'relatedNews' => $this->categoryNews('piyasalar', 4),
            'miniCards' => $this->miniCards(),
            'kapDisclosures' => KapDisclosure::orderByDesc('published_at')->take(6)->get(),
            'converter' => $this->converter->defaultContext(),
        ], $this->layoutData()));
    }

    /**
     * /doviz/pariteler — tüm pariteler tek tablo + gelişen piyasa kurları.
     */
    public function parities(): View
    {
        return view('doviz.parities', array_merge([
            'quickLinks' => self::QUICK_LINKS,
            'parityQuotes' => $this->market->quotes(MarketInstrument::GROUP_PARITY),
            'emergingQuotes' => $this->market->quotes(MarketInstrument::GROUP_EMERGING_FX),
            'relatedNews' => $this->categoryNews('piyasalar', 4),
            'converter' => $this->converter->defaultContext(),
        ], $this->layoutData()));
    }

    /**
     * /doviz/parite/{slug} — parite detay (hero + güniçi + tablolar).
     */
    public function parityDetail(string $slug): View
    {
        $code = $slug === 'dolar-endeksi' ? 'DXY' : strtoupper(str_replace('-', '', $slug));

        $instrument = MarketInstrument::where('group', MarketInstrument::GROUP_PARITY)
            ->where('code', $code)
            ->first();

        // Geriye dönük: katalog slug'ı ile de eşleştir.
        $instrument ??= MarketInstrument::where('group', MarketInstrument::GROUP_PARITY)
            ->where('slug', $slug)
            ->first();

        abort_unless($instrument, 404);

        return view('doviz.detail', array_merge(
            $this->detailData($instrument, isParity: true),
            $this->layoutData()
        ));
    }

    /**
     * /doviz/{slug} — döviz kuru detay (dolar, euro, isvicre-frangi ...).
     */
    public function detail(string $slug): View
    {
        $code = MarketInstrument::CURRENCY_ROUTE_ALIASES[$slug] ?? null;

        $instrument = MarketInstrument::where('group', MarketInstrument::GROUP_CURRENCY)
            ->when($code !== null, fn ($query) => $query->where('code', $code))
            ->when($code === null, fn ($query) => $query->where('slug', $slug))
            ->first();

        abort_unless($instrument, 404);

        return view('doviz.detail', array_merge(
            $this->detailData($instrument, isParity: false),
            $this->layoutData()
        ));
    }

    /**
     * /doviz-cevirici — bağımsız döviz çevirici araç sayfası (spec §3).
     */
    public function converter(): View
    {
        return view('doviz.converter', array_merge([
            'converter' => $this->converter->defaultContext(),
        ], $this->layoutData()));
    }

    /**
     * Detay görünümü ortak verisi (döviz & parite).
     *
     * @return array<string, mixed>
     */
    private function detailData(MarketInstrument $instrument, bool $isParity): array
    {
        $quote = $this->market->quote($instrument);

        $breadcrumb = $isParity
            ? [
                ['label' => 'Döviz', 'url' => '/doviz'],
                ['label' => 'Pariteler', 'url' => '/doviz/pariteler'],
                ['label' => $instrument->name],
            ]
            : [
                ['label' => 'Döviz', 'url' => '/doviz'],
                ['label' => $instrument->name],
            ];

        return [
            'quickLinks' => self::QUICK_LINKS,
            'isParity' => $isParity,
            'instrument' => $instrument,
            'quote' => $quote,
            'breadcrumb' => $breadcrumb,
            'chart' => $this->market->sparklineSvg($instrument, 90, 900, 300),
            'ranges' => ['1G', '1H', 'Tümü'],
            'currencyQuotes' => $this->market->quotes(MarketInstrument::GROUP_CURRENCY),
            'parityQuotes' => $this->parityQuotes(),
            'relatedNews' => $this->categoryNews('piyasalar', 4),
            'featured' => $this->featured(4),
            'converter' => $this->converter->defaultContext(),
        ];
    }

    /**
     * /doviz Pariteler tablosu quote'ları (6 parite + DXY).
     *
     * @return Collection<int, Quote>
     */
    private function parityQuotes(): Collection
    {
        return collect(self::PARITY_CODES)
            ->map(fn (string $code) => $this->market->all()->firstWhere('code', $code))
            ->filter()
            ->map(fn (MarketInstrument $instrument) => $this->market->quote($instrument))
            ->values();
    }

    /**
     * Aside mini kartları (SON %, ALIŞ, SATIŞ ... + sparkline).
     *
     * @return array<int, array{quote: Quote, svg: string}>
     */
    private function miniCards(): array
    {
        $cards = [];

        foreach (self::MINI_CARD_CODES as $code) {
            $instrument = $this->market->all()->firstWhere('code', $code);

            if ($instrument === null) {
                continue;
            }

            $cards[] = [
                'quote' => $this->market->quote($instrument),
                'svg' => $this->market->sparklineSvg($instrument, 40, 320, 80),
            ];
        }

        return $cards;
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
