<?php

namespace Database\Seeders;

use App\Models\BroadcastSchedule;
use App\Models\Program;
use App\Models\ProgramEpisode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * TV programları, bölümler ve yayın akışı (spec §4, §8, §14.3).
 */
class ProgramSeeder extends Seeder
{
    /**
     * Program adı, sunucu, yayın saati (hafta içi).
     */
    public const PROGRAMS = [
        ['60 Dakika', 'Hande Berktan', '19:00'],
        ["Aslı Şafak'la İşin Aslı", 'Aslı Şafak', '20:00'],
        ['Ekonomik Görünüm', 'Hande Berktan', '21:00'],
        ['Küresel Piyasalar', 'Zeynep Arslan', '22:00'],
        ['Spor Kulübü', 'Spor Ekibi', '23:10'],
        ['İlk Söz', 'Sabah Ekibi', '07:00'],
        ['Sabah Raporu', 'Sabah Ekibi', '08:15'],
        ['Yatırım Bülteni', 'Piyasa Ekibi', '09:00'],
        ['Piyasa Masası', 'Piyasa Ekibi', '09:45'],
        ['Yatırım Kulübü', 'Analiz Ekibi', '10:15'],
        ['Grafik Düellosu', 'Teknik Ekibi', '11:00'],
        ['Fokus', 'Analiz Ekibi', '11:15'],
        ['30 Dakika', 'Haber Merkezi', '12:00'],
        ['Girişimcilik Dünyası', 'Girişim Ekibi', '12:30'],
        ['Fon Saati', 'Fon Ekibi', '13:00'],
        ['Seans Ortası', 'Piyasa Ekibi', '13:30'],
        ['Piyasa Hattı', 'Piyasa Ekibi', '14:00'],
        ['İş Dünyası', 'İş Ekibi', '14:45'],
        ['Akıllı Para', 'Yatırım Ekibi', '15:15'],
        ['Finans Merkezi', 'Finans Ekibi', '16:00'],
        ['Kapanışa Doğru', 'Piyasa Ekibi', '17:00'],
        ['Seans Notları', 'Piyasa Ekibi', '17:45'],
        ['Günden Kalanlar', 'Haber Merkezi', '18:20'],
    ];

    public function run(): void
    {
        foreach (self::PROGRAMS as $index => [$name, $host, $airTime]) {
            $program = Program::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'host' => $host,
                    'air_time' => $airTime,
                    'description' => $name.', Bloomberg HT ekranlarında ekonomi gündemini ve piyasa gelişmelerini izleyicilere aktarıyor.',
                    'thumbnail' => '/img/placeholders/placeholder-'.(($index % 8) + 1).'.svg',
                    'day_of_week' => $index < 5 ? 1 : null,
                    'is_live' => $index === 0,
                    'sort_order' => $index,
                    'is_active' => true,
                ]
            );

            for ($e = 0; $e < 2; $e++) {
                $episodeTitle = 'Piyasa değerlendirmesi';
                $airAt = now()->subDays($e)->setTime((int) substr($airTime, 0, 2), (int) substr($airTime, 3, 2));

                ProgramEpisode::updateOrCreate(
                    ['program_id' => $program->id, 'slug' => Str::slug($episodeTitle.' '.$airAt->format('Ymd'))],
                    [
                        'title' => $episodeTitle,
                        'guest' => 'Konuk Analist',
                        'duration_seconds' => 60 * (28 + $e * 4),
                        'thumbnail' => '/img/placeholders/placeholder-'.(($index % 8) + 1).'.svg',
                        'video_url' => 'https://vmcdn.ciner.com.tr/demo/'.$program->slug.'-'.$e.'.mp4',
                        'air_at' => $airAt,
                    ]
                );
            }

            // Hafta içi yayın akışı (Pazartesi-Cuma).
            for ($day = 1; $day <= 5; $day++) {
                BroadcastSchedule::updateOrCreate(
                    ['day_of_week' => $day, 'air_time' => $airTime, 'program_name' => $name],
                    [
                        'program_id' => $program->id,
                        'description' => $name.' yayın akışında.',
                    ]
                );
            }
        }
    }
}