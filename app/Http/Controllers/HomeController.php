<?php

namespace App\Http\Controllers;

use App\Models\BreakingNews;
use App\Models\News;

class HomeController extends Controller
{
    public function index()
    {
        return view('home.index', array_merge([
            'featured' => News::where('status', 'published')
                ->where('is_featured', true)
                ->with(['category', 'author'])
                ->orderByDesc('published_at')
                ->take(6)
                ->get(),
            'latestNews' => News::where('status', 'published')
                ->with(['category', 'author'])
                ->orderByDesc('published_at')
                ->take(24)
                ->get(),
            'latestBreaking' => BreakingNews::where('is_active', true)
                ->orderByDesc('published_at')
                ->take(10)
                ->get(),
        ], $this->layoutData()));
    }
}
