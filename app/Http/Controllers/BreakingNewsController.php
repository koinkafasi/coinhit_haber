<?php

namespace App\Http\Controllers;

use App\Models\BreakingNews;
use Illuminate\Contracts\View\View;

class BreakingNewsController extends Controller
{
    public function index(): View
    {
        return view('breaking.index', array_merge([
            'items' => BreakingNews::with('news')
                ->where('is_active', true)
                ->orderByDesc('published_at')
                ->get(),
        ], $this->layoutData()));
    }
}
