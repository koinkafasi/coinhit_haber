<?php

namespace Database\Seeders;

use App\Models\ChartArchive;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ChartArchiveSeeder extends Seeder
{
    /**
     * 5 grafik arşivi örneği (spec §4 / §8 /grafik-arsivi).
     */
    public const CHARTS = [
        ['Yurt içi yerleşiklerin TL tercihi güçlü seyrini koruyor', 'Piyasa'],
        ['Dolar endeksinde haftalık görünüm', 'Piyasa'],
        ['Enflasyon beklentileri anketi yılın son çeyreğini işaret ediyor', 'Makro Ekonomi'],
        ['Kripto fonlara yönelim artıyor', 'Grafik'],
        ['Tarım emtia fiyatlarındaki değişim', 'Aktüel'],
    ];

    public function run(): void
    {
        foreach (self::CHARTS as $index => [$title, $category]) {
            $chartId = 3792000 + $index;

            ChartArchive::updateOrCreate(
                ['chart_id' => $chartId],
                [
                    'category' => $category,
                    'title' => $title,
                    'slug' => Str::slug($title),
                    'summary' => 'Grafik, '.$category.' kategorisinde son dönemdeki gelişmeleri özetlemektedir.',
                    'published_at' => now()->subDays($index * 3 + 1),
                ]
            );
        }
    }
}
