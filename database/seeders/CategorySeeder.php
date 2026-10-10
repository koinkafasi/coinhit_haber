<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Bloomberg HT kök + yaprak kategori taksonomisi (spec §4).
     */
    public const CATEGORIES = [
        ['HABERLER', 'haberler'],
        ['PİYASALAR', 'piyasalar'],
        ['ENERJİ', 'enerji'],
        ['EKONOMİK VERİLER VE GÜNDEM', 'ekonomik-veriler-ve-gundem'],
        ['FİNANSAL TEKNOLOJİ', 'finansal-teknoloji'],
        ['TARIM', 'tarim'],
        ['ZİRVE', 'zirve'],
        ['KRİPTO', 'kripto'],
        ['SİYASET', 'siyaset'],
        ['SEKTÖR HABERLERİ', 'sektor-haberleri'],
        ['EMTİA', 'emtia'],
        ['GÖRÜŞ', 'gorus'],
        ['Hava Durumu', 'hava-durumu'],
        ['Davos', 'davos'],
        ['Türkiye Ekonomisi', 'turkiye-ekonomisi', 'haberler'],
        ['Küresel Piyasalar', 'kuresel-piyasalar', 'haberler'],
        ['Emtia&Enerji', 'emtia-enerji', 'haberler'],
    ];

    public function run(): void
    {
        $order = 0;

        foreach (self::CATEGORIES as $item) {
            [$name, $slug] = $item;
            $parentSlug = $item[2] ?? null;

            $parent = $parentSlug ? Category::where('slug', $parentSlug)->first() : null;

            Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'parent_id' => $parent?->id,
                    'sort_order' => $order++,
                    'is_active' => true,
                ]
            );
        }
    }
}
