<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Category;
use App\Models\News;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Response;

/**
 * RSS beslemeleri (master spec §3, §11.5).
 *
 * `/rss`, `/rss/tum-haberler.xml` (media:content 1280×720) ve kategori/yazar
 * beslemeleri.
 */
class RssController extends Controller
{
    public function index(): Response
    {
        return $this->feed('Bloomberg HT', 'Bloomberg HT son haberleri', '/rss', false);
    }

    public function all(): Response
    {
        return $this->feed('Bloomberg HT — Tüm Haberler', 'Bloomberg HT tüm haber akışı', '/rss/tum-haberler.xml', true);
    }

    public function category(string $slug): Response
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        return $this->feed(
            'Bloomberg HT — '.$category->name,
            $category->name.' kategorisindeki son haberler',
            '/rss/kategori/'.$slug.'.xml',
            false,
            fn ($query) => $query->where('category_id', $category->id)
        );
    }

    public function author(string $slug): Response
    {
        $author = Author::where('slug', $slug)->firstOrFail();

        return $this->feed(
            'Bloomberg HT — '.$author->name,
            $author->name.' yazıları',
            '/rss/yazar/'.$slug.'.xml',
            false,
            fn ($query) => $query->where('author_id', $author->id)
        );
    }

    /**
     * @param  callable(Builder<News>): void|null  $scope
     */
    private function feed(string $title, string $description, string $path, bool $withMedia, ?callable $scope = null): Response
    {
        $query = News::where('status', 'published')
            ->with(['category', 'author'])
            ->orderByDesc('published_at')
            ->limit(50);

        if ($scope !== null) {
            $scope($query);
        }

        $items = $query->get();

        return response()
            ->view('seo.rss', [
                'title' => $title,
                'description' => $description,
                'selfUrl' => url($path),
                'siteUrl' => url('/'),
                'withMedia' => $withMedia,
                'items' => $items,
            ])
            ->header('Content-Type', 'application/rss+xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=600, stale-while-revalidate=300');
    }
}
