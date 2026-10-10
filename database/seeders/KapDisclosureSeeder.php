<?php

namespace Database\Seeders;

use App\Models\KapDisclosure;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * KAP bildirimleri + TR/EN alan tablosu (spec §4, §8).
 */
class KapDisclosureSeeder extends Seeder
{
    public const COMPANIES = [
        ['GARAN', 'Garanti BBVA'],
        ['AKBNK', 'Akbank'],
        ['THYAO', 'Türk Hava Yolları'],
        ['ASELS', 'Aselsan'],
        ['EREGL', 'Ereğli Demir Çelik'],
        ['TUPRS', 'Tüpraş'],
        ['KCHOL', 'Koç Holding'],
        ['BIMAS', 'BİM Birleşik Mağazalar'],
    ];

    public const TITLES = [
        'Özel Durum Açıklaması (Genel)',
        'Esas Sözleşme Tadili',
        'Kar Payı Dağıtımı',
        'Genel Kurul Toplantısı',
        'Finansal Takvim',
        'Payların Geri Alınması',
        'Sermaye Artırımı',
        'Yönetim Kurulu Kararı',
    ];

    public function run(): void
    {
        foreach (self::COMPANIES as $index => [$code, $name]) {
            $baseId = 349000 + $index * 17;

            foreach (self::TITLES as $tIndex => $title) {
                $disclosure = KapDisclosure::updateOrCreate(
                    ['kap_id' => $baseId + $tIndex],
                    [
                        'company_code' => $code,
                        'company_name' => $name,
                        'company_slug' => Str::slug($name),
                        'title' => $title,
                        'category' => $title,
                        'summary' => $name.' tarafından yapılan '.$title.' bildirimi.',
                        'body' => $name.' A.Ş. tarafından Kamuyu Aydınlatma Platformu\'na yapılan açıklama: '.$title.' kapsamında gerekli bilgilendirme yapılmıştır.',
                        'published_at' => now()->subHours($index * 9 + $tIndex * 2 + 1),
                    ]
                );

                $this->syncFields($disclosure, $title);
            }
        }
    }

    private function syncFields(KapDisclosure $disclosure, string $title): void
    {
        $values = [
            'CONSOLIDATION_METHOD' => ['Yok', 'None'],
            'MaterialEventDisclosureGeneralAbstract' => [$disclosure->summary, $disclosure->summary],
            'UpdateNotificationFlag' => ['Hayır', 'No'],
            'CorrectionNotificationFlag' => ['Hayır', 'No'],
            'DateOfThePreviousNotification' => ['-', '-'],
            'DelayedAnnouncementFlag' => ['Hayır', 'No'],
            'ExplanationTextBlock' => [$disclosure->body, $disclosure->body],
        ];

        $order = 0;

        foreach (KapDisclosure::FIELD_DICTIONARY as $key => [$labelTr, $labelEn]) {
            [$valueTr, $valueEn] = $values[$key] ?? [null, null];

            $disclosure->fields()->updateOrCreate(
                ['key' => $key],
                [
                    'label_tr' => $labelTr,
                    'label_en' => $labelEn,
                    'value_tr' => $valueTr,
                    'value_en' => $valueEn,
                    'sort_order' => $order++,
                ]
            );
        }
    }
}