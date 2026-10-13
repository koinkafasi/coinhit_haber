<?php

namespace App\Http\Controllers;

use App\Models\Fund;
use App\Models\News;
use App\Models\Program;
use App\Services\Fund\FundDataService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Yatırım Fonları bölümü (spec §8, §15.8).
 *
 * `/yatirim-fonlari` (fon haberleri + Fon Saati + Günün Özeti + fon sınıfları +
 * karşılaştırmalı getiriler), fon detay, Fon Karşılaştır ve Fon Portföy
 * Dağılımı araç sayfalarını üretir.
 */
class FundController extends Controller
{
    public function __construct(private readonly FundDataService $funds) {}

    /**
     * /yatirim-fonlari — ana fon bölümü (referans birebir).
     *
     * Üst 2 kolon: ana (fon haber hero slider) + aside 400px (alt-tool nav +
     * advertisement1 reklam çifti) → Fon Saati (swiper) → Günün Özeti (KPI
     * tablo) → Fon Sınıflarında Bugün (7 sınıf sekmesi; her panelde Yükselen/
     * Düşen iki tablo) → Karşılaştırmalı Fon Getirileri (tümü + link).
     */
    public function index(Request $request): View
    {
        $allRows = $this->funds->all();

        return view('fund.index', array_merge([
            'pageTitle' => __('media.fund_title'),
            'fundClasses' => $this->classData(),
            'kpi' => $this->funds->kpi(),
            'fonSaati' => $this->fonSaatiEpisodes(),
            'fundNews' => $this->latestNews(10),
            'returnRows' => $allRows,
            'totalFundCount' => $allRows->count(),
            'toolLinks' => $this->toolLinks(),
        ], $this->layoutData()));
    }

    /**
     * /yatirim-fonlari/fon-siniflarinda-bugun — bağımsız fon sınıfları sayfası.
     */
    public function fundClasses(Request $request): View
    {
        return view('fund.classes', array_merge([
            'pageTitle' => __('media.fund_classes_page_title'),
            'fundClasses' => $this->classData(),
            'totalFundCount' => Fund::where('is_active', true)->count(),
            'toolLinks' => $this->toolLinks(),
        ], $this->layoutData()));
    }

    /**
     * 7 fon sınıfı — her sınıf için Yükselen (10) ve Düşen (10) lider listesi.
     *
     * @return array<int, array{key: string, label: string, rows: array{yukselen: Collection, dusen: Collection}}>
     */
    private function classData(): array
    {
        $classes = [];

        foreach (Fund::classLabels() as $key => $label) {
            $classes[] = [
                'key' => $key,
                'label' => $label,
                'rows' => [
                    'yukselen' => $this->funds->leaderboard($key, 'yukselen', 10),
                    'dusen' => $this->funds->leaderboard($key, 'dusen', 10),
                ],
            ];
        }

        return $classes;
    }

    /**
     * /yatirim-fonlari/fon-karsilastirma — iki fon seç, getiri/risk karşılaştır.
     */
    public function compare(Request $request): View
    {
        $a = $this->resolveFund($request->query('a'));
        $b = $this->resolveFund($request->query('b'), $a?->id);

        return view('fund.compare', array_merge([
            'pageTitle' => __('media.compare_funds'),
            'toolLinks' => $this->toolLinks(),
            'fundOptions' => Fund::where('is_active', true)->orderBy('code')->get(),
            'a' => $a,
            'b' => $b,
            'rows' => ($a && $b) ? $this->funds->compare($a, $b) : [],
            'snapshotA' => $a ? $this->funds->snapshot($a) : null,
            'snapshotB' => $b ? $this->funds->snapshot($b) : null,
        ], $this->layoutData()));
    }

    /**
     * /yatirim-fonlari/fon-portfoy-dagilim — fon portföy dağılım görseli.
     */
    public function portfolio(Request $request): View
    {
        $fund = $this->resolveFund($request->query('fon'));

        return view('fund.portfolio', array_merge([
            'pageTitle' => __('media.fund_portfolio_allocation'),
            'toolLinks' => $this->toolLinks(),
            'fundOptions' => Fund::where('is_active', true)->orderBy('code')->get(),
            'fund' => $fund,
            'snapshot' => $fund ? $this->funds->snapshot($fund) : null,
            'distribution' => $fund ? $fund->portfolioDistribution() : [],
        ], $this->layoutData()));
    }

    /**
     * /yatirim-fonlari/{fon-slug} — fon detay sayfası.
     */
    public function show(string $slug): View
    {
        $fund = Fund::where('slug', $slug)->where('is_active', true)->first();

        abort_unless($fund, 404);

        $related = Fund::where('is_active', true)
            ->where('fund_class', $fund->fund_class)
            ->whereKeyNot($fund->id)
            ->orderBy('sort_order')
            ->take(4)
            ->get();

        return view('fund.detail', array_merge([
            'pageTitle' => $fund->name,
            'fund' => $fund,
            'snapshot' => $this->funds->snapshot($fund),
            'related' => $related,
            'relatedNews' => $this->latestNews(4),
            'toolLinks' => $this->toolLinks(),
        ], $this->layoutData()));
    }

    /**
     * Fon Saati programının son bölümleri (6 video — spec §8).
     *
     * @return Collection<int, \App\Models\ProgramEpisode>
     */
    private function fonSaatiEpisodes(): Collection
    {
        $program = Program::where('slug', 'fon-saati')->first();

        if (! $program) {
            return collect();
        }

        return $program->episodes()->orderByDesc('air_at')->take(6)->get();
    }

    /**
     * @return Collection<int, News>
     */
    private function latestNews(int $take): Collection
    {
        return News::where('status', 'published')
            ->orderByDesc('published_at')
            ->take($take)
            ->get();
    }

    private function resolveFund(?string $code, ?int $excludeId = null): ?Fund
    {
        $query = Fund::where('is_active', true)->orderBy('code');

        if ($code) {
            $query->where('code', $code);
        } elseif ($excludeId !== null) {
            $query->whereKeyNot($excludeId);
        }

        return $query->first();
    }

    /**
     * Alt-tool nav (3 buton) — spec §8, §15.8 (referans birebir: "Fon Sınıflarında
     * Bugün" bağımsız sayfaya gider).
     *
     * @return array<int, array{label: string, url: string, anchor: bool}>
     */
    private function toolLinks(): array
    {
        return [
            ['label' => __('media.compare_funds'), 'url' => '/yatirim-fonlari/fon-karsilastirma', 'anchor' => false],
            ['label' => __('media.fund_classes_today'), 'url' => '/yatirim-fonlari/fon-siniflarinda-bugun', 'anchor' => false],
            ['label' => __('media.fund_portfolio_allocation'), 'url' => '/yatirim-fonlari/fon-portfoy-dagilim', 'anchor' => false],
        ];
    }
}