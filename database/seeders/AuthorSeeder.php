<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;

class AuthorSeeder extends Seeder
{
    /**
     * 6 yazar / görüş köşesi.
     */
    public const AUTHORS = [
        ['İrfan Donat', 'irfan-donat', 'Tarım Analiz', 'Tarım, enerji ve makro ekonomi üzerine yazıyor.'],
        ['Sefer Yüksel', 'sefer-yuksel', 'Finansal Teknoloji', 'Finansal teknoloji ve dijital bankacılık alanında köşe yazıyor.'],
        ['Ayşe Karagöz', 'ayse-karagoz', 'Ekonomi', 'Makro ekonomi politikaları ve merkez bankacılığı üzerine analizler.'],
        ['Mehmet Demir', 'mehmet-demir', 'Piyasalar', 'Borsa, döviz ve faiz piyasalarına ilişkin günlük değerlendirmeler.'],
        ['Zeynep Arslan', 'zeynep-arslan', 'Küresel Piyasalar', 'Küresel piyasalar ve jeopolitik gelişmeler hakkında yazıyor.'],
        ['Kerem Öztürk', 'kerem-ozturk', 'Kripto & Fon', 'Kripto varlıklar ve yatırım fonları üzerine görüşler.'],
    ];

    public function run(): void
    {
        foreach (self::AUTHORS as $index => [$name, $slug, $title, $bio]) {
            Author::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'title' => $title,
                    'bio' => $bio,
                    'sort_order' => $index,
                    'is_active' => true,
                ]
            );
        }
    }
}
