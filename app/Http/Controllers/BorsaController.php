<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\KapDisclosure;
use App\Models\MarketInstrument;
use App\Models\News;
use App\Models\ProgramEpisode;
use App\Services\Market\MarketDataService;
use App\Services\Market\Quote;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Borsa bölümü (spec §8 / §15.3) — hisse, endeks, KAP, dünya borsaları,
 * borsa kapanışı ve alternatif piyasalar sayfaları.
 */
class BorsaController extends Controller
{
    /**
     * BIST endeks seçicide bir sektör endeksi seçildiğinde hisse listesini
     * süzmek için üyelik haritası (spec §5 endeks/hisse ilişkisi).
     *
     * @var array<string, array<int, string>>
     */
    private const SECTOR_MEMBERS = [
        'XBANK' => ['GARAN', 'AKBNK', 'YKBNK', 'ISCTR', 'HALKB', 'VAKBN'],
        'XULAS' => ['THYAO', 'PGSUS', 'TAVHL'],
        'XHOLD' => ['KCHOL', 'SAHOL', 'DOHOL', 'TKFEN'],
        'XILTM' => ['TCELL', 'TTKOM'],
        'XTCRT' => ['BIMAS', 'MGROS', 'SOKM'],
        'XGIDA' => ['AEFES'],
        'XAUTO' => ['FROTO', 'TOASO', 'ARCLK', 'VESTL'],
        'XMADN' => ['KOZAL', 'KOZAA'],
        'XKMYA' => ['TUPRS', 'PETKM', 'GUBRF', 'HEKTS'],
        'XMANA' => ['EREGL', 'ISDMR', 'KRDMD'],
        'XTAST' => ['SISE', 'OYAKC'],
        'XGMYO' => ['EKGYO', 'TRGYO'],
        'XELKT' => ['ODAS', 'ZOREN'],
        'XINSA' => ['ENKAI'],
        'XUTEK' => ['ASELS'],
    ];

    /**
     * /borsa alt-hızlı bağlantıları (spec §15.3).
     *
     * @var array<int, array{label: string, url: string}>
     */
    private const QUICK_LINKS = [
        ['label' => 'BIST', 'url' => '/borsa'],
        ['label' => 'Hisse Senetleri', 'url' => '/borsa/hisseler'],
        ['label' => 'Yükselen/Düşen Hisseler', 'url' => '/borsa/en-cok'],
        ['label' => 'KAP Haberleri', 'url' => '/borsa/hisseler/kap-haberleri'],
        ['label' => 'Dünya Borsaları', 'url' => '/borsa/dunya-borsalari'],
        ['label' => 'Endeksler', 'url' => '/borsa/endeksler'],
        ['label' => 'Borsa Kapanış', 'url' => '/borsa/borsa-kapanis'],
        ['label' => 'Alternatif Piyasalar', 'url' => '/alternatif'],
    ];

    public function __construct(private readonly MarketDataService $market) {}

    /**
     * /borsa — BIST tablosu + Artanlar/Azalanlar/İşlem Görenler + İlgili Haberler.
     */
    public function index(): View
    {
        $stocks = $this->market->quotes(MarketInstrument::GROUP_STOCK);

        return view('borsa.index', array_merge([
            'quickLinks' => self::QUICK_LINKS,
            'bistQuotes' => $this->market->quotes(MarketInstrument::GROUP_BIST_INDEX)->take(15)->values(),
            'gainers' => $this->topBy($stocks, fn (Quote $q) => $q->changePercent, 10),
            'losers' => $this->topBy($stocks, fn (Quote $q) => -$q->changePercent, 10),
            'actives' => $this->topBy($stocks, fn (Quote $q) => $q->volumeTl, 10),
            'relatedNews' => $this->categoryNews('piyasalar', 6),
            'featured' => $this->featured(5),
            // aside
            'kapDisclosures' => KapDisclosure::orderByDesc('published_at')->take(6)->get(),
        ], $this->layoutData()));
    }

    /**
     * /borsa/hisseler ve /borsa/hisseler/{harf} — Endeks dropdown + A-Z + tablo + sayfalama.
     */
    public function stocks(Request $request, ?string $letter = null): View
    {
        $quotes = $this->filteredStocks($request, $letter);
        $paginator = $this->paginateCollection($quotes, $request, 8);

        return view('borsa.stocks', array_merge([
            'quickLinks' => self::QUICK_LINKS,
            'paginator' => $paginator,
            'letter' => $letter ? strtoupper($letter) : null,
            'letters' => $this->letters(),
            'selectedIndex' => (string) $request->query('endeks', ''),
            'indexOptions' => $this->indexOptions(),
            'kapDisclosures' => KapDisclosure::orderByDesc('published_at')->take(6)->get(),
        ], $this->layoutData()));
    }

    /**
     * /borsa/borsa-kapanis ve /borsa/borsa-kapanis/{harf} — A-Z + tarih + tablo + sayfalama.
     */
    public function closing(Request $request, ?string $letter = null): View
    {
        $quotes = $this->filteredStocks($request, $letter);
        $paginator = $this->paginateCollection($quotes, $request, 8);

        return view('borsa.closing', array_merge([
            'quickLinks' => self::QUICK_LINKS,
            'paginator' => $paginator,
            'letter' => $letter ? strtoupper($letter) : null,
            'letters' => $this->letters(),
            'selected' => [
                'gun' => $request->query('gun'),
                'ay' => $request->query('ay'),
                'yil' => $request->query('yil'),
            ],
            'days' => range(1, 31),
            'months' => ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık'],
            'years' => range(2019, 2026),
        ], $this->layoutData()));
    }

    /**
     * /borsa/en-cok — Yükselenler / Düşenler / En Çok İşlem Görenler.
     */
    public function most(): View
    {
        $stocks = $this->market->quotes(MarketInstrument::GROUP_STOCK);

        return view('borsa.most', array_merge([
            'quickLinks' => self::QUICK_LINKS,
            'gainers' => $this->topBy($stocks, fn (Quote $q) => $q->changePercent, 20),
            'losers' => $this->topBy($stocks, fn (Quote $q) => -$q->changePercent, 20),
            'actives' => $this->topBy($stocks, fn (Quote $q) => $q->volumeTl, 20),
        ], $this->layoutData()));
    }

    /**
     * /borsa/hisseler/kap-haberleri — ters kronolojik KAP akışı + videolar + pariteler.
     */
    public function kapNews(Request $request): View
    {
        return view('borsa.kap-news', array_merge([
            'quickLinks' => self::QUICK_LINKS,
            'disclosures' => KapDisclosure::orderByDesc('published_at')->paginate(20)->withQueryString(),
            'videos' => ProgramEpisode::with('program')->orderByDesc('air_at')->take(2)->get(),
            'parities' => $this->paritySidebar(),
        ], $this->layoutData()));
    }

    /**
     * /borsa/dunya-borsalari — Amerika/Avrupa/Asya + vadeliler + CME notu.
     */
    public function world(): View
    {
        $world = $this->market->quotes(MarketInstrument::GROUP_WORLD_INDEX)->keyBy(fn (Quote $q) => $q->code());

        $groups = [
            ['key' => 'amerika', 'title' => 'Amerika', 'codes' => ['DOW', 'SP500', 'NASDAQ']],
            ['key' => 'avrupa', 'title' => 'Avrupa', 'codes' => ['DAX', 'FTSE100', 'CAC40']],
            ['key' => 'asya', 'title' => 'Asya Pasifik', 'codes' => ['SHANGHAI', 'NIKKEI225', 'KOSPI', 'HANGSENG']],
            ['key' => 'vadeliler', 'title' => 'Vadeliler', 'codes' => ['SPFUT', 'NASDAQFUT']],
        ];

        $tables = [];

        foreach ($groups as $group) {
            $tables[] = [
                'key' => $group['key'],
                'title' => $group['title'],
                'quotes' => collect($group['codes'])->map(fn (string $code) => $world->get($code))->filter()->values(),
            ];
        }

        return view('borsa.world', array_merge([
            'quickLinks' => self::QUICK_LINKS,
            'tables' => $tables,
            'cmeNote' => $this->market->sourceNote('cme'),
        ], $this->layoutData()));
    }

    /**
     * /borsa/endeksler — 48 endeks tek tablo (ENDEKSLER, SON, %, AYLIK%, YILLIK%).
     */
    public function indices(): View
    {
        return view('borsa.indices', array_merge([
            'quickLinks' => self::QUICK_LINKS,
            'quotes' => $this->market->quotes(MarketInstrument::GROUP_BIST_INDEX),
        ], $this->layoutData()));
    }

    /**
     * /borsa/endeks/{slug} — endeks detay (fiyat + güniçi + endeks listesi).
     */
    public function indexDetail(string $slug): View
    {
        $instrument = MarketInstrument::where('slug', $slug)
            ->whereIn('group', [MarketInstrument::GROUP_BIST_INDEX, MarketInstrument::GROUP_WORLD_INDEX, MarketInstrument::GROUP_ALTERNATIVE])
            ->first();

        abort_unless($instrument, 404);

        $quote = $this->market->quote($instrument);

        return view('borsa.index-detail', array_merge([
            'quickLinks' => self::QUICK_LINKS,
            'instrument' => $instrument,
            'quote' => $quote,
            'series' => $this->market->sparkline($instrument, 48, 900),
            'chart' => $this->market->sparklineSvg($instrument, 90, 900, 300),
            'ranges' => ['1G', '1H', 'Tümü'],
            'indices' => $this->market->quotes(MarketInstrument::GROUP_BIST_INDEX),
            'relatedNews' => $this->categoryNews('piyasalar', 4),
        ], $this->layoutData()));
    }

    /**
     * /alternatif — Baltık grubu + Alternatif (VIX, BCOM) + Öne Çıkanlar.
     */
    public function alternative(): View
    {
        return view('borsa.alternative', array_merge([
            'quickLinks' => self::QUICK_LINKS,
            'baltic' => $this->market->quotes(MarketInstrument::GROUP_BALTIC),
            'alternatives' => $this->market->quotes(MarketInstrument::GROUP_ALTERNATIVE),
            'featured' => $this->featured(4),
            'relatedNews' => $this->categoryNews('piyasalar', 4),
        ], $this->layoutData()));
    }

    /**
     * /borsa/hisse/{kod}-{slug}-detay — hisse detay + istatistik kutusu + güniçi grafik.
     */
    public function stock(string $company): View
    {
        // Rota: `/borsa/hisse/<kod>-<slug>-detay` (spec §3) — kod ilk parçadır.
        $code = strtoupper(Str::before($company, '-'));

        $instrument = MarketInstrument::where('group', MarketInstrument::GROUP_STOCK)
            ->where('code', $code)
            ->first();

        abort_unless($instrument, 404);

        $quote = $this->market->quote($instrument);

        return view('borsa.stock-detail', array_merge([
            'quickLinks' => self::QUICK_LINKS,
            'instrument' => $instrument,
            'quote' => $quote,
            'series' => $this->market->sparkline($instrument, 48, 900),
            'chart' => $this->market->sparklineSvg($instrument, 90, 900, 300),
            'ranges' => ['1G', '1H', 'Tümü'],
            'indices' => $this->market->quotes(MarketInstrument::GROUP_BIST_INDEX),
            'indexOptions' => $this->indexOptions(),
            'letters' => $this->letters(),
            'indicesPreview' => $this->market->quotes(MarketInstrument::GROUP_BIST_INDEX)->take(10)->values(),
            'kapDisclosures' => KapDisclosure::where('company_code', $instrument->code)->orderByDesc('published_at')->take(6)->get(),
            'relatedNews' => $this->categoryNews('piyasalar', 6),
        ], $this->layoutData()));
    }

    /**
     * /borsa/hisse/{firma}/kap-haberi/{id} — KAP detay (TR/EN alan tablosu).
     */
    public function kapDetail(string $company, string $kapId): View
    {
        abort_unless(ctype_digit($kapId), 404);

        $disclosure = KapDisclosure::with('fields')
            ->where('company_slug', $company)
            ->where('kap_id', (int) $kapId)
            ->first();

        abort_unless($disclosure, 404);

        return view('borsa.kap-detail', array_merge([
            'disclosure' => $disclosure,
            'relatedDisclosures' => KapDisclosure::where('company_slug', $company)
                ->where('id', '!=', $disclosure->id)
                ->orderByDesc('published_at')
                ->take(8)
                ->get(),
        ], $this->layoutData()));
    }

    /**
     * Bir grup için istenen ölçüte göre azalan sıralı ilk N quote.
     *
     * @param  Collection<int, Quote>  $quotes
     * @param  callable(Quote): (int|float)  $metric
     * @return Collection<int, Quote>
     */
    private function topBy(Collection $quotes, callable $metric, int $take): Collection
    {
        return $quotes->sortByDesc($metric)->take($take)->values();
    }

    /**
     * Hisse listesini harf + endeks parametrelerine göre süzer.
     *
     * @return Collection<int, Quote>
     */
    private function filteredStocks(Request $request, ?string $letter): Collection
    {
        $quotes = $this->market->quotes(MarketInstrument::GROUP_STOCK);

        if ($letter !== null && $letter !== '' && strcasecmp($letter, 'tumu') !== 0) {
            $prefix = Str::upper(Str::ascii($letter));
            $quotes = $quotes->filter(fn (Quote $q) => Str::startsWith(Str::upper($q->code()), $prefix));
        }

        $endeks = (string) $request->query('endeks', '');

        if ($endeks !== '') {
            $index = $this->market->instruments(MarketInstrument::GROUP_BIST_INDEX)->firstWhere('slug', $endeks);

            if ($index && isset(self::SECTOR_MEMBERS[$index->code])) {
                $members = self::SECTOR_MEMBERS[$index->code];
                $quotes = $quotes->filter(fn (Quote $q) => in_array($q->code(), $members, true));
            }
        }

        return $quotes->values();
    }

    /**
     * @param  Collection<int, Quote>  $items
     * @return LengthAwarePaginator<int, Quote>
     */
    private function paginateCollection(Collection $items, Request $request, int $perPage): LengthAwarePaginator
    {
        $page = max((int) $request->query('sayfa', 1), 1);

        $paginator = new LengthAwarePaginator(
            $items->slice(($page - 1) * $perPage, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()],
        );
        $paginator->setPageName('sayfa');

        return $paginator->withQueryString();
    }

    /**
     * A-Z harf sekmeleri (Türk alfabesine yakın BIST harf seti).
     *
     * @return array<int, string>
     */
    private function letters(): array
    {
        return str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ');
    }

    /**
     * Endeks seçiniz dropdown seçenekleri (BIST + bölgesel endeksler).
     *
     * @return Collection<int, MarketInstrument>
     */
    private function indexOptions(): Collection
    {
        return $this->market->instruments(MarketInstrument::GROUP_BIST_INDEX);
    }

    /**
     * KAP/paylaşım kenar çubuğu pariteleri (BIST100, USD/TRY, ..., BRENT).
     *
     * @return Collection<int, Quote>
     */
    private function paritySidebar(): Collection
    {
        $codes = ['XU100', 'USDTRY', 'EURTRY', 'EURUSD', 'TR10Y', 'GAU', 'BRENT'];

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