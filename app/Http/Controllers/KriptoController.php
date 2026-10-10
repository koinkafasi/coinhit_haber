<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MarketInstrument;
use App\Models\News;
use App\Services\Market\MarketDataService;
use App\Services\Market\Quote;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Kripto bölümü (spec §5, §8, §14.5 / §15.6) — hero haber kartları, A-Z filtre,
 * kripto para tablosu, hızlı widget paneli ve BTC/ETH/XRP detay sayfaları.
 */
class KriptoController extends Controller
{
    /**
     * Aside hızlı widget paneli enstrümanları (BTC/ETH/XRP — spec §15.6).
     *
     * @var array<int, string>
     */
    private const QUICK_CARD_CODES = ['BTC', 'ETH', 'XRP'];

    public function __construct(private readonly MarketDataService $market) {}

    /**
     * /kripto ve /kripto/liste/{harf} — hero kartlar + A-Z + tablo + widget.
     */
    public function index(?string $harf = null): View
    {
        $all = $this->market->quotes(MarketInstrument::GROUP_CRYPTO);
        $letter = $harf !== null && $harf !== '' ? Str::upper(Str::ascii($harf)) : null;

        $quotes = $letter !== null
            ? $all->filter(fn (Quote $q) => $this->matchesLetter($q, $letter))->values()
            : $all;

        return view('kripto.index', array_merge([
            'quotes' => $quotes,
            'allQuotes' => $all,
            'letter' => $letter,
            'letters' => str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ'),
            'hero' => $this->heroNews(12),
            'relatedNews' => $this->categoryNews('kripto', 4, 'piyasalar'),
            'quickCards' => $this->quickCards(),
            'note' => $this->market->sourceNote('crypto'),
        ], $this->layoutData()));
    }

    /**
     * Tek kripto para detay görünümü (BTC/ETH/XRP — `/doviz/<slug>` rotasından).
     */
    public function detail(MarketInstrument $instrument): View
    {
        $quote = $this->market->quote($instrument);

        return view('kripto.detail', array_merge([
            'instrument' => $instrument,
            'quote' => $quote,
            'breadcrumb' => [
                ['label' => 'Kripto', 'url' => '/kripto'],
                ['label' => $instrument->name],
            ],
            'chart' => $this->market->sparklineSvg($instrument, 90, 900, 300),
            'ranges' => ['1G', '1H', 'Tümü'],
            'cryptoQuotes' => $this->market->quotes(MarketInstrument::GROUP_CRYPTO),
            'relatedNews' => $this->categoryNews('kripto', 4, 'piyasalar'),
            'featured' => $this->featured(4),
            'quickCards' => $this->quickCards(),
            'note' => $this->market->sourceNote('crypto'),
        ], $this->layoutData()));
    }

    /**
     * Bir kripto satırının A-Z filtresine uyup uymadığı (ad veya kod).
     */
    private function matchesLetter(Quote $quote, string $letter): bool
    {
        if (Str::startsWith(Str::upper($quote->code()), $letter)) {
            return true;
        }

        return Str::startsWith(Str::upper(Str::ascii($quote->instrument->name)), $letter);
    }

    /**
     * Hero haber kartları (12 adet; kripto → piyasalar → son haberler).
     *
     * @return Collection<int, News>
     */
    private function heroNews(int $take): Collection
    {
        $items = $this->categoryNews('kripto', $take);

        if ($items->count() < $take) {
            $extra = News::where('status', 'published')
                ->whereNotIn('id', $items->pluck('id'))
                ->orderByDesc('published_at')
                ->take($take - $items->count())
                ->get();

            $items = $items->concat($extra);
        }

        return $items->values();
    }

    /**
     * BTC/ETH/XRP hızlı widget kartları (fiyat + % + sparkline).
     *
     * @return array<int, array{quote: Quote, svg: string}>
     */
    private function quickCards(): array
    {
        $cards = [];

        foreach (self::QUICK_CARD_CODES as $code) {
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
    private function categoryNews(string $slug, int $take, ?string $fallback = null): Collection
    {
        $category = Category::where('slug', $slug)->first();
        $items = collect();

        if ($category) {
            $items = News::where('status', 'published')
                ->where('category_id', $category->id)
                ->orderByDesc('published_at')
                ->take($take)
                ->get();
        }

        if ($items->isEmpty() && $fallback !== null) {
            $fallbackCategory = Category::where('slug', $fallback)->first();

            if ($fallbackCategory) {
                $items = News::where('status', 'published')
                    ->where('category_id', $fallbackCategory->id)
                    ->orderByDesc('published_at')
                    ->take($take)
                    ->get();
            }
        }

        return $items;
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
