<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\BuildsMarketWidget;
use App\Models\News;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SearchController extends Controller
{
    use BuildsMarketWidget;

    public function index(Request $request, ?string $q = null): View|JsonResponse
    {
        $query = trim((string) ($q ?? $request->query('q', '')));

        if ($query === '') {
            $results = News::whereRaw('1 = 0')->paginate(24);
        } else {
            $results = $this->search($query)->paginate(24)->withQueryString();
        }

        if ($this->wantsJson($request)) {
            return $this->jsonPayload($query, $results);
        }

        return view('search.index', array_merge([
            'query' => $query,
            'results' => $results,
            'relatedTags' => $this->relatedTags(),
        ], $this->marketWidgetData(), $this->layoutData()));
    }

    /**
     * /haberleri/{tag} — etiket listeleme sayfası (spec §8 / §3).
     */
    public function tag(Request $request, string $tag): View|JsonResponse
    {
        $label = $this->humanize($tag);

        $results = $this->search($tag, $tag)->paginate(24)->withQueryString();

        if ($this->wantsJson($request)) {
            return $this->jsonPayload($label, $results);
        }

        return view('search.tag', array_merge([
            'tag' => $tag,
            'label' => $label,
            'results' => $results,
            'relatedTags' => $this->relatedTags($tag),
        ], $this->marketWidgetData(), $this->layoutData()));
    }

    /**
     * @return Builder<News>
     */
    private function search(string $query, ?string $tag = null)
    {
        $like = '%'.$query.'%';
        $spaced = '%'.str_replace('-', ' ', $query).'%';

        return News::where('status', 'published')
            ->where(function ($builder) use ($like, $spaced, $tag) {
                if ($tag !== null) {
                    $builder->whereJsonContains('tags', $tag);
                }

                $builder->orWhere('title', 'like', $like)
                    ->orWhere('title', 'like', $spaced)
                    ->orWhere('spot', 'like', $like)
                    ->orWhere('body', 'like', $like);
            })
            ->with(['category', 'author'])
            ->orderByDesc('published_at');
    }

    /**
     * Arama/tag sonuçlarının "İlgili Etiketler" kümesi.
     *
     * @return Collection<int, string>
     */
    private function relatedTags(?string $except = null): Collection
    {
        return News::where('status', 'published')
            ->orderByDesc('published_at')
            ->take(120)
            ->pluck('tags')
            ->flatten()
            ->filter(fn ($tag) => is_string($tag) && $tag !== '' && $tag !== $except)
            ->countBy()
            ->sortDesc()
            ->take(12)
            ->keys()
            ->values();
    }

    private function humanize(string $tag): string
    {
        return ucwords(str_replace('-', ' ', $tag));
    }

    private function wantsJson(Request $request): bool
    {
        return $request->wantsJson() || $request->query('format') === 'json';
    }

    private function jsonPayload(string $query, $results): JsonResponse
    {
        return response()->json([
            'query' => $query,
            'total' => $results->total(),
            'page' => $results->currentPage(),
            'last_page' => $results->lastPage(),
            'updated_at' => now()->toIso8601String(),
            'results' => $results->getCollection()->map(fn (News $news) => [
                'id' => $news->news_id,
                'title' => $news->title,
                'slug' => $news->slug,
                'url' => $news->url,
                'category' => $news->category?->name,
                'summary' => strip_tags((string) $news->spot),
                'published_at' => $news->published_at?->toIso8601String(),
            ])->values(),
        ]);
    }
}
