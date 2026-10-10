<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\Trend;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Trend / hızlı link etiketleri ve ilişkili haberler (spec §4, §7 program 7, §8 /trendler).
 */
class TrendSeeder extends Seeder
{
    public const TRENDS = [
        'Rezerv' => 'Merkez bankası rezervleri ve döviz rezervlerine ilişkin gelişmeler.',
        'Hisse' => 'Borsa ve hisse senedi piyasalarındaki öne çıkan haberler.',
        'Waller' => 'Fed yetkilisi Waller ve para politikası açıklamaları.',
        'Fon ödemeleri' => 'Yatırım fonları ve fon ödemelerine dair gelişmeler.',
        'FTSE' => 'İngiltere FTSE endeksi ve küresel borsa etkileri.',
        'Enflasyon' => 'Enflasyon verileri, beklentiler ve fiyatlama gelişmeleri.',
    ];

    public function run(): void
    {
        $order = 0;

        foreach (self::TRENDS as $label => $description) {
            $trend = Trend::updateOrCreate(
                ['slug' => Str::slug($label)],
                [
                    'label' => $label,
                    'description' => $description,
                    'sort_order' => $order,
                    'is_active' => true,
                ]
            );

            $slug = Str::slug($label);

            $news = News::where('status', 'published')
                ->where(function ($query) use ($label, $slug) {
                    $query->where('title', 'like', '%'.$label.'%')
                        ->orWhereJsonContains('tags', $slug);
                })
                ->orderByDesc('published_at')
                ->take(6)
                ->pluck('id');

            if ($news->isEmpty()) {
                $news = News::where('status', 'published')
                    ->orderByDesc('published_at')
                    ->take(4)
                    ->pluck('id');
            }

            $trend->news()->sync($news->all());

            $order++;
        }
    }
}