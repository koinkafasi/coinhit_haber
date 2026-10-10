<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsHomeWidgets;
use App\Http\Controllers\Concerns\BuildsMarketWidget;
use App\Models\Category;
use App\Models\News;
use App\Support\Locale;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;

class NewsController extends Controller
{
    use BuildsHomeWidgets, BuildsMarketWidget;

    /**
     * /haberler — 2 sütunlu masonry haber grid'i (spec §8 / §15.10).
     */
    public function index(): View
    {
        $news = News::where('status', 'published')
            ->with(['category', 'author'])
            ->orderByDesc('published_at')
            ->paginate(24);

        return view('news.index', array_merge([
            'news' => $news,
            'lead' => $news->first(),
        ], $this->layoutData()));
    }

    /**
     * /tumhaberler — tek sütun liste + sayfalama + Piyasalar widget (spec §8 / §15.12).
     */
    public function all(): View
    {
        $news = News::where('status', 'published')
            ->with(['category', 'author'])
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('news.all', array_merge([
            'news' => $news,
            'mostReadTabs' => $this->mostReadTabs(8),
        ], $this->marketWidgetData(), $this->layoutData()));
    }

    /**
     * Makale detay `/{slug}-{id}` (spec §9, §11, §15.15).
     */
    public function show(string $slug, string $newsId): View
    {
        abort_unless(ctype_digit($newsId), 404);

        $news = News::with(['category', 'author'])
            ->where('news_id', (int) $newsId)
            ->where(function ($query) use ($slug) {
                $query->where('slug', $slug)
                    ->orWhereHas('translations', function ($translation) use ($slug) {
                        $translation->where('slug', $slug);
                    });
            })
            ->where('status', 'published')
            ->first();

        abort_unless($news, 404);

        $related = News::with(['category', 'author'])
            ->where('category_id', $news->category_id)
            ->where('id', '!=', $news->id)
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->take(10)
            ->get();

        $featured = News::with(['category', 'author'])
            ->where('status', 'published')
            ->where('id', '!=', $news->id)
            ->orderByDesc('is_featured')
            ->orderByDesc('views')
            ->take(8)
            ->get();

        return view('news.show', array_merge([
            'news' => $news,
            'related' => $related,
            'featured' => $featured,
            'categoryUrl' => $this->categoryUrl($news->category),
            'pageType' => 'article',
        ], $this->layoutData()));
    }

    /**
     * `/infinite/news/{newsId}` — bir sonraki makalenin HTML parçası (spec §3, §9, §15.15).
     */
    public function infinite(string $newsId): Response
    {
        abort_unless(ctype_digit($newsId), 404);

        $news = News::with(['category', 'author'])
            ->where('news_id', (int) $newsId)
            ->where('status', 'published')
            ->first();

        if ($news === null) {
            return response('<div data-infinite-end></div>', 200)
                ->header('Content-Type', 'text/html; charset=UTF-8');
        }

        return response()->view('news.infinite', [
            'news' => $news,
            'categoryUrl' => $this->categoryUrl($news->category),
        ])->header('Content-Type', 'text/html; charset=UTF-8');
    }

    /**
     * Kategori breadcrumb hedefi — kanal rotası varsa ona, yoksa /haberler'e.
     */
    private function categoryUrl(?Category $category): string
    {
        if ($category === null) {
            return Locale::url('/haberler');
        }

        if ($category->slug === 'haberler' || array_key_exists((string) $category->slug, ChannelController::CHANNELS)) {
            return Locale::url('/'.$category->slug);
        }

        return Locale::url('/haberler');
    }
}