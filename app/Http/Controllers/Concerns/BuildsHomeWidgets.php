<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Category;
use App\Models\News;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

/**
 * Global widget veri üreticisi (spec §6, §7 modül 5/22/25, §8 /trendler):
 * En Çok Okunanlar (Bugün|Bu Hafta|Bu Ay), Öne Çıkanlar ve
 * "Piyasa/Emtia/Sektör/Siyaset Haberleri" haber modülü.
 */
trait BuildsHomeWidgets
{
    /**
     * En Çok Okunanlar sekmeleri: Bugün | Bu Hafta | Bu Ay.
     *
     * @return array<string, Collection<int, News>>
     */
    protected function mostReadTabs(int $take = 8): array
    {
        return [
            'today' => $this->mostRead('today', $take),
            'week' => $this->mostRead('week', $take),
            'month' => $this->mostRead('month', $take),
        ];
    }

    /**
     * Okunma sayısına göre sıralı en çok okunan haberler (spec §6, §7 modül 25).
     *
     * @return Collection<int, News>
     */
    protected function mostRead(string $period, int $take = 8): Collection
    {
        $since = match ($period) {
            'today' => CarbonImmutable::now()->subDay(),
            'week' => CarbonImmutable::now()->subWeek(),
            default => CarbonImmutable::now()->subMonth(),
        };

        $items = News::where('status', 'published')
            ->with('category')
            ->where('published_at', '>=', $since)
            ->orderByDesc('views')
            ->take($take)
            ->get();

        if ($items->isEmpty()) {
            $items = News::where('status', 'published')
                ->with('category')
                ->orderByDesc('views')
                ->take($take)
                ->get();
        }

        return $items;
    }

    /**
     * Öne Çıkanlar widget'ı (spec §7 modül 5/10, §9 aside).
     *
     * @return Collection<int, News>
     */
    protected function featuredNews(int $take = 6): Collection
    {
        return News::where('status', 'published')
            ->with('category')
            ->orderByDesc('is_featured')
            ->orderByDesc('views')
            ->take($take)
            ->get();
    }

    /**
     * Kategoriye bağlı haber modülü (Piyasa/Emtia/Sektör/Siyaset Haberleri).
     *
     * @return Collection<int, News>
     */
    protected function categoryNewsModule(string $slug, int $take = 5): Collection
    {
        $category = Category::where('slug', $slug)->first();

        if ($category === null) {
            return collect();
        }

        return News::where('status', 'published')
            ->where('category_id', $category->id)
            ->with(['category', 'author'])
            ->orderByDesc('published_at')
            ->take($take)
            ->get();
    }
}