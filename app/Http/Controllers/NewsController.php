<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Contracts\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        return view('news.index', array_merge([
            'news' => News::where('status', 'published')
                ->with(['category', 'author'])
                ->orderByDesc('published_at')
                ->paginate(24),
        ], $this->layoutData()));
    }

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

        $related = News::where('category_id', $news->category_id)
            ->where('id', '!=', $news->id)
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->take(6)
            ->get();

        return view('news.show', array_merge([
            'news' => $news,
            'related' => $related,
        ], $this->layoutData()));
    }
}
