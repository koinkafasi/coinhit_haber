<?php

namespace Database\Seeders;

use App\Models\BreakingNews;
use App\Models\News;
use Illuminate\Database\Seeder;

class BreakingNewsSeeder extends Seeder
{
    /**
     * 10 son dakika şeridi başlığı (spec §2.3 / §4).
     */
    public const TITLES = [
        'BIST 100 günü yükselişle kapattı',
        'Dolar/TL 28,50 seviyesinde dengelendi',
        'Brent petrol 90 doların üzerinde seyrediyor',
        'TCMB rezervleri artışını sürdürüyor',
        'İstanbul için sağanak yağış uyarısı yapıldı',
        'Altın ons fiyatı 2.400 doları test ediyor',
        'Hazine ihalesine güçlü talep geldi',
        'Türkiye kredi notu görünümü iyileştirildi',
        'Borsa İstanbul için yeni halka arz başvurusu yapıldı',
        'Elektrik tüketiminde rekor seviye',
    ];

    public function run(): void
    {
        foreach (self::TITLES as $index => $title) {
            $newsTitle = str_replace([' ile ', ' için ', ' yapıldı', ' geldi', ' yapıldı'], ' ', $title);
            $news = News::where('title', $newsTitle)->orWhere('title', 'like', '%'.str($newsTitle)->limit(20).'%')->first();
            $publishedAt = now()->subMinutes($index * 37 + 12);

            BreakingNews::updateOrCreate(
                ['title' => $title],
                [
                    'news_id' => $news?->id,
                    'short_code' => 'sn'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
                    'published_at' => $publishedAt,
                    'date_group' => $publishedAt->toDateString(),
                    'sort_order' => $index,
                    'is_active' => true,
                ]
            );
        }
    }
}
