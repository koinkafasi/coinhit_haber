<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\ProgramEpisode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * TV programları, video serileri ve bölümleri (spec §4, §8, §14.3).
 *
 * Yayın akışı satırları BroadcastScheduleSeeder tarafından bu programlara
 * bağlanır; burada referans verilen her program adı için bir kayıt üretilir.
 */
class ProgramSeeder extends Seeder
{
    /**
     * TV programları: [ad, sunucu, yayın saati, slug?, is_series].
     * Prime kuşak doğrulanmış yazımlar (spec §14.3).
     */
    public const PROGRAMS = [
        // Prime kuşak
        ['60 Dakika', 'Hande Berktan', '19:00', null, false],
        ["Aslı Şafak'la İşin Aslı", 'Aslı Şafak', '20:00', 'asli-safakla-isin-asli', true],
        ['Ekonomik Görünüm', 'Hande Berktan', '21:00', null, false],
        ['Küresel Piyasalar', 'Zeynep Arslan', '22:00', null, false],
        ['Spor Kulübü', 'Spor Ekibi', '23:10', null, true],
        // Sabah kuşağı
        ['İlk Söz', 'Sabah Ekibi', '07:00', null, false],
        ['Sabah Raporu', 'Sabah Ekibi', '08:15', null, false],
        ['Yatırım Bülteni', 'Piyasa Ekibi', '09:00', null, false],
        ['Piyasa Masası', 'Piyasa Ekibi', '09:45', null, false],
        ['Yatırım Kulübü', 'Analiz Ekibi', '10:15', null, false],
        ['Grafik Düellosu', 'Teknik Ekibi', '11:00', null, false],
        ['Fokus', 'Analiz Ekibi', '11:15', null, false],
        // Öğle / öğleden sonra
        ['30 Dakika', 'Haber Merkezi', '12:00', null, false],
        ['Girişimcilik Dünyası', 'Girişim Ekibi', '12:30', null, false],
        ['Fon Saati', 'Fon Ekibi', '13:00', null, true],
        ['Seans Ortası', 'Piyasa Ekibi', '13:30', null, false],
        ['Piyasa Hattı', 'Piyasa Ekibi', '14:00', null, false],
        ['İş Dünyası', 'İş Ekibi', '14:45', null, false],
        ['Akıllı Para', 'Yatırım Ekibi', '15:15', null, false],
        ['Finans Merkezi', 'Finans Ekibi', '16:00', null, false],
        ['Kapanışa Doğru', 'Piyasa Ekibi', '17:00', null, false],
        ['Seans Notları', 'Piyasa Ekibi', '17:45', null, false],
        ['Günden Kalanlar', 'Haber Merkezi', '18:20', null, false],
        // Gece tekrarları / cumartesi-pazar
        ['HT Kulüp', 'Ece Saruhan', '01:30', null, false],
        ['Varlık Yönetimi', 'Yatırım Ekibi', '03:10', null, false],
        ['Aile Ekonomisi', 'Aile Ekibi', '04:00', null, false],
        ['Akıllı Tarım', 'Tarım Ekibi', '04:50', null, false],
        ['Üst Düzey', 'Hande Berktan', '23:30', null, true],
        ['Gündem Teknoloji', 'Sefer Yüksel', '05:20', null, true],
        ['Tarım Analiz', 'İrfan Donat', '09:00', null, false],
        ['Bloomberg Investigates', 'Bloomberg HT', '22:30', null, false],
        ['Gözde Atasoy ile İşin Zirvesinde', 'Gözde Atasoy', '18:30', 'isin-zirvesinde', false],
        ['Eko Tarım', 'Tarım Ekibi', '08:15', null, false],
        // Video serileri (yalnız dijital yayın)
        ['Oyun Dışı', 'Spor Ekibi', null, 'oyun-disi', true],
        ["İş'te Zirve", 'Gözde Atasoy', null, 'iste-zirve', true],
        ['Yarının Zekası', 'Teknoloji Ekibi', null, null, true],
        ['Tarih Keyfi', 'Tarih Ekibi', null, null, true],
        ['Dört Kadın Bir Dünya', 'Kadın Ekibi', null, null, true],
        ["Pandora'nın Merakı", 'Pandora Ekibi', null, 'pandoranin-meraki', true],
        ['Akustikhane', 'Müzik Ekibi', null, null, true],
        ['Açık Hava Sineması', 'Sinema Ekibi', null, null, true],
        ['Finansal Teknoloji', 'Sefer Yüksel', null, 'finansal-teknoloji', true],
        ['Haber', 'Haber Merkezi', null, null, true],
        ["Ece Saruhan'la Oyuna Devam", 'Ece Saruhan', null, 'ece-saruhanla-oyuna-devam', true],
        ['Macera Sever', 'Gezi Ekibi', null, null, true],
    ];

    /**
     * Seri bölüm konuları — dizin seri başına bölüm üretmede kullanılır.
     */
    private const EPISODE_TOPICS = [
        'Haftanın piyasa değerlendirmesi',
        'Uzman konukla gündem sohbeti',
        'Öne çıkan gelişmeler ve analiz',
        'Yatırımcıya yol haritası',
        'Sektör dinamiği ve beklentiler',
        'Makro veriler ışığında görünüm',
        'Riskler ve fırsatlar',
        'Küresel piyasalarda son durum',
    ];

    private const GUESTS = [
        'Ali Yılmaz', 'Zeynep Kaya', 'Mehmet Demir', 'Elif Şahin',
        'Burak Öz', 'Selin Aydın', 'Kerem Yıldız', 'Deniz Arslan',
    ];

    public function run(): void
    {
        foreach (self::PROGRAMS as $index => [$name, $host, $airTime, $slug, $isSeries]) {
            $program = Program::updateOrCreate(
                ['slug' => $slug ?: Str::slug($name)],
                [
                    'name' => $name,
                    'host' => $host,
                    'air_time' => $airTime,
                    'description' => $name.', Bloomberg HT ekranlarında ekonomi gündemini ve piyasa gelişmelerini izleyicilere aktarıyor.',
                    'thumbnail' => '/img/placeholders/placeholder-'.(($index % 8) + 1).'.svg',
                    'day_of_week' => $isSeries ? null : 1,
                    'is_live' => $index === 0,
                    'is_series' => $isSeries,
                    'sort_order' => $index,
                    'is_active' => true,
                ]
            );

            $this->seedEpisodes($program, $index, $airTime, $isSeries);
        }
    }

    /**
     * Bir program/seri için bölümleri üretir.
     */
    private function seedEpisodes(Program $program, int $index, ?string $airTime, bool $isSeries): void
    {
        // Fon Saati programı 6 bölümle listelenir (spec §8 fon bölümü).
        if ($program->slug === 'fon-saati') {
            $this->seedFonSaati($program, $index, $airTime);

            return;
        }

        $count = $isSeries ? 8 : 2;
        $hour = $airTime ? (int) substr($airTime, 0, 2) : 12;
        $minute = $airTime ? (int) substr($airTime, 3, 2) : 0;

        for ($e = 0; $e < $count; $e++) {
            $topic = self::EPISODE_TOPICS[($index + $e) % count(self::EPISODE_TOPICS)];
            $guest = self::GUESTS[($index + $e) % count(self::GUESTS)];
            $airAt = now()->subDays($e * 2 + 1)->setTime($hour, $minute);

            ProgramEpisode::updateOrCreate(
                ['program_id' => $program->id, 'slug' => Str::slug($topic.' '.$airAt->format('Ymd'))],
                [
                    'title' => $topic,
                    'guest' => $guest,
                    'duration_seconds' => 60 * (22 + (($index + $e) % 20)),
                    'thumbnail' => '/img/placeholders/placeholder-'.(($index % 8) + 1).'.svg',
                    'video_url' => 'https://vmcdn.ciner.com.tr/demo/'.$program->slug.'-'.$e.'.mp4',
                    'air_at' => $airAt,
                ]
            );
        }
    }

    /**
     * Fon Saati serisinin 6 bölümü (spec §8).
     */
    private function seedFonSaati(Program $program, int $index, ?string $airTime): void
    {
        $titles = [
            'Piyasa değerlendirmesi',
            'Fon seçiminde dikkat edilecekler',
            'Portföy dağılımı nasıl yapılır',
            'Faiz indiriminin fonlara etkisi',
            'Altın fonlarında son durum',
            'Katılım fonları ve getirileri',
        ];

        foreach ($titles as $e => $title) {
            $airAt = now()->subDays($e)->setTime(13, 0);

            ProgramEpisode::updateOrCreate(
                ['program_id' => $program->id, 'slug' => Str::slug($title.' '.$airAt->format('Ymd'))],
                [
                    'title' => $title,
                    'guest' => $e === 0 ? 'Konuk Analist' : 'Fon Ekibi',
                    'duration_seconds' => 60 * (28 + $e * 4),
                    'thumbnail' => '/img/placeholders/placeholder-'.(($index % 8) + 1).'.svg',
                    'video_url' => 'https://vmcdn.ciner.com.tr/demo/'.$program->slug.'-'.$e.'.mp4',
                    'air_at' => $airAt,
                ]
            );
        }
    }
}