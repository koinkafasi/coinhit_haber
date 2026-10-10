<?php

namespace App\Http\Controllers;

use App\Models\BreakingNews;
use App\Models\Category;
use App\Models\SiteSetting;

abstract class Controller
{
    /**
     * Tüm Blade görünümleri için ortak çerçeve verisi.
     *
     * @return array<string, mixed>
     */
    protected function layoutData(): array
    {
        return [
            'navCategories' => Category::with('children')
                ->whereNull('parent_id')
                ->orderBy('sort_order')
                ->get(),
            'breakingList' => BreakingNews::where('is_active', true)
                ->orderByDesc('published_at')
                ->take(10)
                ->get(),
            'siteSettings' => SiteSetting::pluck('value', 'key')->all(),
        ];
    }
}
