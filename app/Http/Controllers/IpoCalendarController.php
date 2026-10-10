<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\IpoCalendar;
use App\Models\News;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class IpoCalendarController extends Controller
{
    /**
     * /halka-arz-takvimi — Talep Toplama Tarihleri tablosu + haberler
     * (spec §4 / §8).
     */
    public function index(): View
    {
        return view('ipo-calendar.index', array_merge([
            'items' => IpoCalendar::orderBy('sort_order')
                ->orderBy('request_start_date')
                ->get(),
            'related' => $this->byCategory('piyasalar', 4),
            'featured' => $this->featured(4),
        ], $this->layoutData()));
    }

    /**
     * @return Collection<int, News>
     */
    private function byCategory(string $slug, int $take): Collection
    {
        $categoryId = Category::where('slug', $slug)->value('id');

        if ($categoryId === null) {
            return collect();
        }

        return News::where('status', 'published')
            ->where('category_id', $categoryId)
            ->orderByDesc('published_at')
            ->take($take)
            ->get();
    }

    /**
     * @return Collection<int, News>
     */
    private function featured(int $take): Collection
    {
        return News::where('status', 'published')
            ->orderByDesc('published_at')
            ->take($take)
            ->get();
    }
}
