<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsHomeWidgets;
use App\Models\ChartArchive;
use App\Support\Locale;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChartArchiveController extends Controller
{
    use BuildsHomeWidgets;

    /**
     * Kategori sekmeleri (spec §4, §8, §15.14).
     *
     * @var array<string, string>
     */
    public const CATEGORIES = [
        'grafik' => 'Grafik',
        'piyasa' => 'Piyasa',
        'makro-ekonomi' => 'Makro Ekonomi',
        'aktuel' => 'Aktüel',
    ];

    /**
     * /grafik-arsivi — Son Eklenen Grafikler + kategori sekmeleri + grid + sayfalama.
     */
    public function index(Request $request, ?int $page = null): View
    {
        $page = max(1, (int) ($page ?? $request->query('sayfa', 1)));

        $active = $this->resolveCategory($request->query('kategori'));
        $activeSlug = $active !== null ? array_search($active, self::CATEGORIES, true) : null;

        $latest = ChartArchive::orderByDesc('published_at')->take(3)->get();

        $query = ChartArchive::query()
            ->when($active !== null, fn ($builder) => $builder->where('category', $active))
            ->orderByDesc('published_at');

        $perPage = 9;
        $total = (clone $query)->count();
        $charts = $query->forPage($page, $perPage)->get();
        $lastPage = max(1, (int) ceil($total / $perPage));

        return view('charts.index', array_merge([
            'latestCharts' => $latest,
            'charts' => $charts,
            'categories' => self::CATEGORIES,
            'activeCategory' => $activeSlug,
            'currentPage' => $page,
            'lastPage' => $lastPage,
            'chartCount' => $total,
            'mostReadTabs' => $this->mostReadTabs(6),
        ], $this->layoutData()));
    }

    /**
     * `/grafik-arsivi/{slug}-{id}` — grafik detayı (başlık + kategori + görsel + açıklama + ilgili).
     */
    public function show(string $slug, string $chartId): View
    {
        abort_unless(ctype_digit($chartId), 404);

        $chart = ChartArchive::where('chart_id', (int) $chartId)
            ->where('slug', $slug)
            ->first();

        abort_unless($chart, 404);

        $related = ChartArchive::where('id', '!=', $chart->id)
            ->where('category', $chart->category)
            ->orderByDesc('published_at')
            ->take(6)
            ->get();

        if ($related->isEmpty()) {
            $related = ChartArchive::where('id', '!=', $chart->id)
                ->orderByDesc('published_at')
                ->take(6)
                ->get();
        }

        return view('charts.show', array_merge([
            'chart' => $chart,
            'related' => $related,
        ], $this->layoutData()));
    }

    /**
     * Kategori sekmesi URL'i (sayfa kökü, seçili kategori).
     */
    public function categoryUrl(string $slug, ?int $page = null): string
    {
        $path = '/grafik-arsivi'.($page !== null && $page > 1 ? '/'.$page : '');

        return Locale::url($path).'?kategori='.$slug;
    }

    /**
     * `kategori` sorgusunu (slug veya etiket) doğrular ve etikete çevirir.
     */
    private function resolveCategory(mixed $raw): ?string
    {
        if (! is_string($raw) || $raw === '') {
            return null;
        }

        if (array_key_exists($raw, self::CATEGORIES)) {
            return self::CATEGORIES[$raw];
        }

        $label = Str::slug($raw);

        foreach (self::CATEGORIES as $slug => $name) {
            if ($slug === $label || Str::slug($name) === $label) {
                return $name;
            }
        }

        return null;
    }
}