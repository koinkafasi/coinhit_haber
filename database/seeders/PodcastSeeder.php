<?php

namespace Database\Seeders;

use App\Models\Podcast;
use App\Models\PodcastEpisode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Podcast serileri ve bölümleri (spec §4, §7 program 18).
 */
class PodcastSeeder extends Seeder
{
    public const SERIES = [
        ['Bu Fikir Tutar', 'Her Cuma', '18:00'],
        ['Çağlar Ötesi', 'Her Salı', '17:30'],
        ['Metasal Konular', 'Her Çarşamba', '16:00'],
        ['Sabah Raporu', 'Her Sabah', '08:00'],
        ['Sermaye Pusulası', 'Her Perşembe', '19:00'],
        ['Ekonomiye Dair', 'Her Pazartesi', '18:30'],
        ['Piyasa Notları', 'Her Cuma', '20:00'],
        ['Kripto Gündemi', 'Her Çarşamba', '21:00'],
        ['Tarımın Sesi', 'İki haftada bir', '10:00'],
        ['Fintech Sohbetleri', 'Her Salı', '15:00'],
        ['Küresel Bakış', 'Her Perşembe', '20:30'],
        ['Genç Yatırımcı', 'Her Pazar', '14:00'],
    ];

    public function run(): void
    {
        foreach (self::SERIES as $index => [$title, $days, $time]) {
            $podcast = Podcast::updateOrCreate(
                ['slug' => Str::slug($title)],
                [
                    'title' => $title,
                    'description' => $title.' podcast serisinde ekonomi ve piyasalara dair güncel değerlendirmeler dinleyicilerle buluşuyor.',
                    'cover' => '/img/placeholders/placeholder-'.(($index % 8) + 1).'.svg',
                    'air_days' => $days,
                    'air_time' => $time,
                    'sort_order' => $index,
                    'is_active' => true,
                ]
            );

            for ($e = 1; $e <= 3; $e++) {
                $episodeTitle = $e.'. bölüm: '.$title.' özel yayını';

                PodcastEpisode::updateOrCreate(
                    ['podcast_id' => $podcast->id, 'slug' => Str::slug($episodeTitle)],
                    [
                        'title' => $episodeTitle,
                        'description' => $title.' serisinin '.$e.'. bölümü.',
                        'duration_seconds' => 60 * (22 + $e * 3),
                        'audio_url' => 'https://podcast.bloomberght.com/'.$podcast->slug.'/'.$e.'.mp3',
                        'published_at' => now()->subDays($e * 7 + $index),
                    ]
                );
            }
        }
    }
}