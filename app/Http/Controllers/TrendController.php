<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsMarketWidget;
use App\Models\News;
use App\Models\Trend;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

/**
 * /trendler — "# Hızlı Linkler" etiket çipleri + trend haber listesi (spec §8 / §15.13).
 */
class TrendController extends Controller
{
    use BuildsMarketWidget;

    public function index(): View
    {
        return $this->render(null);
    }

    public function show(string $slug): View
    {
        return $this->render($slug);
    }

    private function render(?string $activeSlug): View
    {
        $trends = Trend::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $panels = [];

        foreach ($trends as $trend) {
            $panels[$trend->slug] = [
                'trend' => $trend,
                'items' => $trend->news()
                    ->where('status', 'published')
                    ->with('category')
                    ->orderByDesc('published_at')
                    ->take(12)
                    ->get(),
            ];
        }

        if ($activeSlug !== null && ! array_key_exists($activeSlug, $panels)) {
            abort(404);
        }

        return view('trendler.index', array_merge([
            'trends' => $trends,
            'panels' => $panels,
            'allItems' => $this->allTrendNews(),
            'activeSlug' => $activeSlug,
        ], $this->marketWidgetData(), $this->layoutData()));
    }

    /**
     * "Tümü" sekmesi: aktif trendlere bağlı en yeni haberler.
     *
     * @return Collection<int, News>
     */
    private function allTrendNews(int $take = 12): Collection
    {
        $items = News::where('status', 'published')
            ->with('category')
            ->whereHas('trends', fn ($query) => $query->where('is_active', true))
            ->orderByDesc('published_at')
            ->take($take)
            ->get();

        if ($items->isEmpty()) {
            $items = News::where('status', 'published')
                ->with('category')
                ->orderByDesc('published_at')
                ->take($take)
                ->get();
        }

        return $items;
    }
}