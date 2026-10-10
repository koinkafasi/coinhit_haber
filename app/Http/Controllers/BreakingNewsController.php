<?php

namespace App\Http\Controllers;

use App\Models\BreakingNews;
use Illuminate\Contracts\View\View;

class BreakingNewsController extends Controller
{
    /**
     * /sondakika — ters kronolojik son dakika akışı + paylaş çubuğu
     * (spec §8 / §15.11).
     */
    public function index(): View
    {
        $items = BreakingNews::with('news')
            ->where('is_active', true)
            ->orderByDesc('published_at')
            ->paginate(20);

        return view('breaking.index', array_merge([
            'items' => $items,
        ], $this->layoutData()));
    }
}
