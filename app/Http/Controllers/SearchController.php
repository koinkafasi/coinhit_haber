<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request, ?string $q = null): View
    {
        $query = trim((string) ($q ?? $request->query('q', '')));

        if ($query === '') {
            $results = News::whereRaw('1 = 0')->paginate(24);
        } else {
            $like = '%'.$query.'%';

            $results = News::where('status', 'published')
                ->where(function ($builder) use ($like) {
                    $builder->where('title', 'like', $like)
                        ->orWhere('spot', 'like', $like)
                        ->orWhere('body', 'like', $like);
                })
                ->with(['category', 'author'])
                ->orderByDesc('published_at')
                ->paginate(24)
                ->withQueryString();
        }

        return view('search.index', array_merge([
            'query' => $query,
            'results' => $results,
        ], $this->layoutData()));
    }
}