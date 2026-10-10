<?php

namespace Database\Seeders;

use App\Models\IpoCalendar;
use Illuminate\Database\Seeder;

/**
 * Halka arz takvimi (spec §4, §8 — Talep Tarihleri | Şirket(ticker) | Değer(Lot) | Fiyat).
 */
class IpoCalendarSeeder extends Seeder
{
    public const IPOS = [
        ['Anadolu Enerji A.Ş.', 'ANENR', 24_000_000, 42.50],
        ['Marmara Gıda A.Ş.', 'MRMGD', 18_500_000, 28.00],
        ['Teknoloji Vadi A.Ş.', 'TKVAD', 42_000_000, 15.75],
        ['Ege Kimya A.Ş.', 'EGKMY', 12_750_000, 63.20],
        ['Başkent Lojistik A.Ş.', 'BSKLJ', 30_000_000, 21.40],
        ['Akdeniz Turizm A.Ş.', 'AKTRZ', 9_600_000, 55.00],
        ['Karadeniz Çimento A.Ş.', 'KRCMT', 27_500_000, 33.80],
        ['Anadolu Sağlık A.Ş.', 'ANSAĞ', 15_300_000, 47.90],
    ];

    public function run(): void
    {
        foreach (self::IPOS as $index => [$company, $ticker, $lot, $price]) {
            $start = now()->addDays($index * 3 + 2);
            $end = $start->copy()->addDays(2);

            IpoCalendar::updateOrCreate(
                ['company_name' => $company],
                [
                    'ticker' => $ticker,
                    'request_start_date' => $start->format('d.m.Y'),
                    'request_end_date' => $end->format('d.m.Y'),
                    'lot_value' => $lot,
                    'price' => $price,
                    'status' => $index < 2 ? 'active' : 'planned',
                    'sort_order' => $index,
                ]
            );
        }
    }
}