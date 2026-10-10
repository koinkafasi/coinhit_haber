<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Gallery;
use App\Models\GalleryItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Foto haber / galeri örnek verisi (spec §4, §7, §8).
 */
class GallerySeeder extends Seeder
{
    public const GALLERIES = [
        'Borsa İstanbul’da haftanın öne çıkan hisseleri',
        'Türkiye’nin en büyük sanayi yatırımları',
        'Dünya borsalarından kareler',
        'Altın fiyatlarında hareketli günler',
        'Enerji yatırımlarında yeni dönem',
    ];

    public function run(): void
    {
        $category = Category::where('slug', 'piyasalar')->first()
            ?? Category::where('slug', 'haberler')->first();

        foreach (self::GALLERIES as $index => $title) {
            $gallery = Gallery::updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'category_id' => $category?->id,
                    'title' => $title,
                    'summary' => $title.' başlıklı foto haber galerisi.',
                    'cover' => '/img/placeholders/placeholder-'.(($index % 8) + 1).'.svg',
                    'photo_total_count' => 6,
                    'published_at' => now()->subHours($index * 11 + 3),
                ]
            );

            for ($i = 1; $i <= 6; $i++) {
                GalleryItem::updateOrCreate(
                    ['gallery_id' => $gallery->id, 'sort_order' => $i],
                    [
                        'image' => '/img/placeholders/placeholder-'.((($index + $i) % 8) + 1).'.svg',
                        'caption' => $title.' - '.$i.'. kare',
                    ]
                );
            }
        }
    }
}