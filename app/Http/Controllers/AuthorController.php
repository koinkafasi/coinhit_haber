<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\News;
use App\Support\Locale;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;

class AuthorController extends Controller
{
    /**
     * /yazarlar — tüm yazarlar (mevcut sayfa).
     */
    public function index(): View
    {
        return view('authors.index', array_merge([
            'authors' => Author::withCount('news')
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
        ], $this->layoutData()));
    }

    /**
     * /gorus — yazar/görüş kartları (spec §8 /gorus).
     */
    public function opinions(): View
    {
        $authors = Author::withCount('news')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $authors->each(function (Author $author): void {
            $author->setRelation('latestNews', $this->latestFor($author));
        });

        return view('authors.opinions', array_merge([
            'authors' => $authors,
        ], $this->layoutData()));
    }

    /**
     * /yorum/{author}/{segment} — yazar arşivi (spec §3 / §8); tekil köşe
     * yazısı id'si verilirse makaleye yönlendirir.
     */
    public function archive(string $author, string $segment): View|RedirectResponse
    {
        $model = Author::where('slug', $author)->where('is_active', true)->first();

        abort_unless($model, 404);

        if (ctype_digit($segment)) {
            $target = News::where('author_id', $model->id)
                ->where('news_id', (int) $segment)
                ->where('status', 'published')
                ->first();

            if ($target !== null) {
                return redirect()->to($target->url);
            }
        }

        $news = News::where('author_id', $model->id)
            ->where('status', 'published')
            ->with('category')
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('authors.archive', array_merge([
            'author' => $model,
            'news' => $news,
        ], $this->layoutData()));
    }

    /**
     * @return Collection<int, News>
     */
    private function latestFor(Author $author): Collection
    {
        return News::where('author_id', $author->id)
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->take(4)
            ->get();
    }

    /**
     * Yazar kartlarındaki makale bağlantısı (spec §8 /gorus).
     */
    public static function articleUrl(Author $author): string
    {
        $latest = $author->relationLoaded('latestNews')
            ? $author->getRelation('latestNews')->first()
            : null;

        return Locale::url('/yorum/'.$author->slug.'/'.($latest?->news_id ?? $author->id));
    }

    /**
     * Yazar arşivi bağlantısı ("TÜM YAZILAR").
     */
    public static function archiveUrl(Author $author): string
    {
        return Locale::url('/yorum/'.$author->slug.'/'.$author->id);
    }
}
