<?php

namespace Database\Seeders;

use App\Models\BroadcastSchedule;
use App\Models\Program;
use Illuminate\Database\Seeder;

/**
 * 7 günlük TV yayın akışı (spec §8, §14.3, §15.9).
 *
 * Her gün 00:30–05:20 gece tekrar bandıyla başlar; sabah, öğle ve prime
 * kuşak satırlarıyla devam eder. Cuma prime'ında Akustikhane, hafta sonunda
 * Açık Hava Sineması / Pandora'nın Merakı varyantları uygulanır.
 */
class BroadcastScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $programs = Program::all()->keyBy('name');

        foreach ($this->schedule() as $day => $rows) {
            foreach ($rows as [$time, $name, $description]) {
                $program = $programs->get($name);

                BroadcastSchedule::updateOrCreate(
                    ['day_of_week' => $day, 'air_time' => $time, 'program_name' => $name],
                    [
                        'program_id' => $program?->id,
                        'description' => $description,
                    ]
                );
            }
        }
    }

    /**
     * Gün (1=Pazartesi … 7=Pazar) → saat sıralı satırlar.
     *
     * @return array<int, array<int, array{0: string, 1: string, 2: string}>>
     */
    private function schedule(): array
    {
        $weekday = $this->weekdayRows();
        $friday = $this->weekdayRows(['23:10' => ['Akustikhane', 'Müzik ve sohbetle haftayı kapatıyoruz.']]);
        $weekend = $this->weekendRows(false);
        $sunday = $this->weekendRows(true);

        return [
            1 => $weekday,
            2 => $weekday,
            3 => $weekday,
            4 => $weekday,
            5 => $friday,
            6 => $weekend,
            7 => $sunday,
        ];
    }

    /**
     * Hafta içi (Pazartesi–Cuma) satırları. $overrides ile belirli saatler
     * değiştirilir (örn. Cuma 23:10 → Akustikhane).
     *
     * @param  array<string, array{0: string, 1: string}>  $overrides
     * @return array<int, array{0: string, 1: string, 2: string}>
     */
    private function weekdayRows(array $overrides = []): array
    {
        $rows = [
            // Gece tekrar bandı
            ['00:30', "Aslı Şafak'la İşin Aslı", 'Günün öne çıkan konularının tekrarı.'],
            ['01:30', 'HT Kulüp', 'Ekonomi ve yaşam üzerine sohbetin tekrarı.'],
            ['02:20', 'Fon Saati', 'Fon dünyasındaki gelişmelerin tekrarı.'],
            ['03:10', 'Varlık Yönetimi', 'Varlık yönetimi stratejileri ve portföy önerileri.'],
            ['04:00', 'Aile Ekonomisi', 'Bütçe yönetimi ve tasarruf önerileri.'],
            ['04:50', 'Akıllı Tarım', 'Tarım ekonomisinde öne çıkan başlıklar.'],
            ['05:20', 'Gündem Teknoloji', 'Teknoloji gündeminin gece tekrarı.'],
            // Sabah kuşağı
            ['06:10', "Aslı Şafak'la İşin Aslı", 'Sabah kuşağı öncesi gündem tekrarı.'],
            ['07:00', 'İlk Söz', 'Güne başlarken ekonomi ve piyasa başlıkları.'],
            ['08:15', 'Sabah Raporu', 'Yurt içi ve yurt dışı piyasaların sabah görünümü.'],
            ['09:00', 'Yatırım Bülteni', 'Günün yatırım gündemi ve veri takvimi.'],
            ['09:45', 'Piyasa Masası', 'Analistlerle piyasa masasında değerlendirme.'],
            ['10:15', 'Yatırım Kulübü', 'Yatırımcılar için strateji ve fırsatlar.'],
            ['11:00', 'Grafik Düellosu', 'Teknik analizde destek ve direnç noktaları.'],
            ['11:15', 'Fokus', 'Günün öne çıkan ekonomi başlıkları.'],
            // Öğle / öğleden sonra
            ['12:00', '30 Dakika', 'Ekonomi gündeminin kısa ve öz özeti.'],
            ['12:30', 'Girişimcilik Dünyası', 'Girişimcilik ekosistemi ve yatırım haberleri.'],
            ['13:00', 'Fon Saati', 'Fon piyasasında son gelişmeler ve uzman yorumları.'],
            ['13:30', 'Seans Ortası', 'Borsada seans ortası değerlendirmesi.'],
            ['14:00', 'Piyasa Hattı', 'Piyasalarda anlık gelişmeler.'],
            ['14:45', 'İş Dünyası', 'Şirket haberleri ve sektörel gelişmeler.'],
            ['15:15', 'Akıllı Para', 'Akıllı yatırımın ipuçları.'],
            ['16:00', 'Finans Merkezi', 'Finans dünyasından öne çıkan başlıklar.'],
            ['17:00', 'Kapanışa Doğru', 'Borsa kapanışı öncesi son değerlendirme.'],
            ['17:45', 'Seans Notları', 'Seansın öne çıkan hisse ve endeksleri.'],
            ['18:20', 'Günden Kalanlar', 'Günün ekonomi gündemi ve yarının takvimi.'],
            // Prime kuşak
            ['19:00', '60 Dakika', 'Günün ekonomi gündemi derinlemesine.'],
            ['20:00', "Aslı Şafak'la İşin Aslı", 'Güncel ekonomi ve piyasa sohbeti.'],
            ['21:00', 'Ekonomik Görünüm', 'Makro ekonomi ve gelecek beklentileri.'],
            ['22:00', 'Küresel Piyasalar', 'Dünya piyasalarında son durum.'],
            ['23:10', 'Spor Kulübü', 'Spor dünyası ve ekonomi ilişkisi.'],
        ];

        return array_map(function (array $row) use ($overrides): array {
            $time = $row[0];

            if (isset($overrides[$time])) {
                return [$time, $overrides[$time][0], $overrides[$time][1]];
            }

            return $row;
        }, $rows);
    }

    /**
     * Hafta sonu (Cumartesi / Pazar) satırları.
     *
     * @return array<int, array{0: string, 1: string, 2: string}>
     */
    private function weekendRows(bool $sunday): array
    {
        return [
            // Gece tekrar bandı
            ['00:30', 'Üst Düzey', 'Haftanın üst düzey konuk sohbetlerinin tekrarı.'],
            ['01:30', 'HT Kulüp', 'Ekonomi ve yaşam üzerine sohbetin tekrarı.'],
            ['02:20', 'Fon Saati', 'Fon dünyasındaki gelişmelerin tekrarı.'],
            ['03:10', 'Varlık Yönetimi', 'Varlık yönetimi stratejileri ve portföy önerileri.'],
            ['04:00', 'Aile Ekonomisi', 'Bütçe yönetimi ve tasarruf önerileri.'],
            ['04:50', 'Akıllı Tarım', 'Tarım ekonomisinde öne çıkan başlıklar.'],
            ['05:20', 'Gündem Teknoloji', 'Teknoloji gündeminin gece tekrarı.'],
            // Sabah kuşağı
            ['07:00', 'Üst Düzey', 'İş dünyasının üst düzey yöneticileriyle sohbet.'],
            ['08:15', 'Eko Tarım', 'Tarım ve gıda ekonomisinde haftanın özeti.'],
            ['09:00', 'Tarım Analiz', 'İrfan Donat ile tarım piyasaları analizi.'],
            ['09:45', 'Bloomberg Investigates', 'Bloomberg özel araştırma dosyaları.'],
            ['10:15', "Ece Saruhan'la Oyuna Devam", 'Oyun ve eğlence dünyasından haberler.'],
            ['11:15', 'Macera Sever', 'Gezi ve keşif programı.'],
            // Öğle / öğleden sonra
            ['12:00', '30 Dakika', 'Haftanın ekonomi gündeminin özeti.'],
            ['12:30', 'Girişimcilik Dünyası', 'Girişimcilik ekosistemi ve yatırım haberleri.'],
            ['13:00', 'Fon Saati', 'Fon piyasasında haftanın değerlendirmesi.'],
            ['13:30', 'Aile Ekonomisi', 'Bütçe yönetimi ve tasarruf önerileri.'],
            ['14:45', 'Varlık Yönetimi', 'Portföy çeşitlendirme ve varlık dağılımı.'],
            ['15:15', 'Akıllı Tarım', 'Tarım teknolojileri ve verimlilik.'],
            ['16:00', 'HT Kulüp', 'Ekonomi ve yaşam üzerine keyifli sohbet.'],
            ['17:00', 'Gözde Atasoy ile İşin Zirvesinde', 'İş dünyasının zirvesindeki isimler.'],
            ['18:20', 'Günden Kalanlar', 'Haftanın ekonomi gündemi ve yarının takvimi.'],
            // Prime kuşak
            ['19:00', 'Açık Hava Sineması', 'Açık havada sinema keyfi.'],
            ['20:30', "Pandora'nın Merakı", 'Merak edilen konuların peşinde.'],
            ['22:00', 'Küresel Piyasalar', 'Dünya piyasalarında son durum.'],
            ['23:10', $sunday ? 'Spor Kulübü' : 'Akustikhane', $sunday ? 'Haftanın spor gündemi.' : 'Müzik ve sohbetle haftayı kapatıyoruz.'],
        ];
    }
}