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
     * /yatirim-fonlari — ana fon bölümü.
     */
    public function index(Request $request): View
    {
        $direction = $request->query('yon') === 'dusen' ? 'dusen' : 'yukselen';
        $showAll = $request->boolean('tumu');

        $classes = [];
        foreach (Fund::CLASSES as $key => $label) {
            $classes[] = [
                'key' => $key,
                'label' => $label,
                'rows' => $this->funds->leaderboard($key, $direction, 10),
            ];
        }

        $allRows = $this->funds->all();

        return view('fund.index', array_merge([
            'pageTitle' => 'Yatırım Fonları',
            'direction' => $direction,
            'fundClasses' => $classes,
            'kpi' => $this->funds->kpi(),
            'fonSaati' => $this->fonSaatiEpisodes(),
            'fundNews' => $this->latestNews(9),
            'returnRows' => $showAll ? $allRows : $allRows->take(15),
            'totalFundCount' => $allRows->count(),
            'showAll' => $showAll,
            'toolLinks' => $this->toolLinks(),
        ], $this->layoutData()));
    }

    /**
     * /yatirim-fonlari/fon-karsilastirma — iki fon seç, getiri/risk karşılaştır.
     */
    public function compare(Request $request): View
    {
        $a = $this->resolveFund($request->query('a'));
        $b = $this->resolveFund($request->query('b'), $a?->id);

        return view('fund.compare', array_merge([
            'pageTitle' => 'Fon Karşılaştır',
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
            'pageTitle' => 'Fon Portföy Dağılımı',
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
     * Alt-tool nav (3 buton) — spec §8, §15.8.
     *
     * @return array<int, array{label: string, url: string, anchor: bool}>
     */
    private function toolLinks(): array
    {
        return [
            ['label' => 'Fon Karşılaştır', 'url' => '/yatirim-fonlari/fon-karsilastirma', 'anchor' => false],
            ['label' => 'Fon Sınıflarında Bugün', 'url' => '/yatirim-fonlari#fon-siniflari', 'anchor' => true],
            ['label' => 'Fon Portföy Dağılımı', 'url' => '/yatirim-fonlari/fon-portfoy-dagilim', 'anchor' => false],
        ];
    }
}