<?php

namespace Database\Seeders;

use App\Models\Fund;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Yatırım fonları kataloğu (spec §5 fon verisi, §8 `/yatirim-fonlari`).
 *
 * 7 fon sınıfına dağılmış gerçekçi fon kodları/adları; fiyat, dönemsel getiri
 * matrisi, risk değeri ve portföy dağılımı deterministik olarak üretilir.
 */
class FundSeeder extends Seeder
{
    /**
     * Sınıf => [risk tabanı, oynaklık katsayısı, fiyat üretimi (taban, aralık)].
     */
    private const CLASS_PROFILE = [
        'karma-ve-degisken' => ['risk' => 4, 'vol' => 3.2, 'price' => [1.5, 12.0]],
        'borclanma-araclari' => ['risk' => 2, 'vol' => 1.2, 'price' => [0.8, 6.0]],
        'fon-sepeti' => ['risk' => 3, 'vol' => 2.0, 'price' => [0.6, 4.0]],
        'hisse-senedi' => ['risk' => 6, 'vol' => 5.0, 'price' => [2.0, 30.0]],
        'katilim' => ['risk' => 4, 'vol' => 3.0, 'price' => [1.0, 9.0]],
        'kiymetli-madenler' => ['risk' => 5, 'vol' => 4.2, 'price' => [0.5, 3.5]],
        'para-piyasasi' => ['risk' => 1, 'vol' => 0.4, 'price' => [0.05, 0.4]],
    ];

    /**
     * Sınıf => fon listesi [kod, ad, tür].
     */
    private const FUNDS = [
        'karma-ve-degisken' => [
            ['TCD', 'Tacirler Portföy Değişken Fon', 'Değişken Fon'],
            ['KHA', 'Kare Portföy Karma Fon', 'Karma Fon'],
            ['AAV', 'Azimut Piyasası Değişken Fon', 'Değişken Fon'],
            ['GDP', 'Garanti Portföy Değişken Fon', 'Değişken Fon'],
            ['HVS', 'HSBC Portföy Değişken Fon', 'Değişken Fon'],
            ['MCD', 'Marmara Capital Değişken Fon', 'Değişken Fon'],
        ],
        'borclanma-araclari' => [
            ['TBT', 'TEB Portföy Borçlanma Araçları Fonu', 'Borçlanma Araçları Fonu'],
            ['AKB', 'Ak Portföy Borçlanma Araçları Fonu', 'Borçlanma Araçları Fonu'],
            ['GBN', 'Garanti Portföy Borçlanma Araçları Fonu', 'Borçlanma Araçları Fonu'],
            ['YBD', 'Yapı Kredi Borçlanma Araçları Fonu', 'Borçlanma Araçları Fonu'],
            ['ZBB', 'Ziraat Portföy Borçlanma Araçları Fonu', 'Borçlanma Araçları Fonu'],
            ['IBT', 'İş Portföy Borçlanma Araçları Fonu', 'Borçlanma Araçları Fonu'],
        ],
        'fon-sepeti' => [
            ['FSE', 'Ak Portföy Fon Sepeti Fonu', 'Fon Sepeti Fonu'],
            ['GFS', 'Garanti Portföy Fon Sepeti Fonu', 'Fon Sepeti Fonu'],
            ['YFS', 'Yapı Kredi Fon Sepeti Fonu', 'Fon Sepeti Fonu'],
            ['TFS', 'TEB Portföy Fon Sepeti Fonu', 'Fon Sepeti Fonu'],
            ['ZFS', 'Ziraat Portföy Fon Sepeti Fonu', 'Fon Sepeti Fonu'],
        ],
        'hisse-senedi' => [
            ['AFA', 'Ak Portföy Hisse Senedi Fonu', 'Hisse Senedi Yoğun Fon'],
            ['GAF', 'Garanti Portföy Hisse Senedi Fonu', 'Hisse Senedi Yoğun Fon'],
            ['TKF', 'İş Portföy Hisse Senedi Fonu', 'Hisse Senedi Yoğun Fon'],
            ['YAF', 'Yapı Kredi Hisse Senedi Fonu', 'Hisse Senedi Yoğun Fon'],
            ['HDF', 'HSBC Portföy Hisse Senedi Fonu', 'Hisse Senedi Yoğun Fon'],
            ['DFA', 'Deniz Portföy Hisse Senedi Fonu', 'Hisse Senedi Yoğun Fon'],
            ['MPS', 'Marmara Portföy Hisse Senedi Fonu', 'Hisse Senedi Yoğun Fon'],
        ],
        'katilim' => [
            ['KZL', 'Kızılbük Katılım Hisse Senedi Fonu', 'Katılım Fonu'],
            ['KUT', 'KT Portföy Katılım Fonu', 'Katılım Fonu'],
            ['ZKF', 'Ziraat Katılım Fonu', 'Katılım Fonu'],
            ['TKU', 'Türkiye Katılım Fonu', 'Katılım Fonu'],
            ['AKU', 'Ak Portföy Katılım Fonu', 'Katılım Fonu'],
        ],
        'kiymetli-madenler' => [
            ['ALT', 'Altın Fonu', 'Kıymetli Madenler Fonu'],
            ['GLD', 'Ak Portföy Altın Fonu', 'Kıymetli Madenler Fonu'],
            ['ZGD', 'Ziraat Portföy Altın Fonu', 'Kıymetli Madenler Fonu'],
            ['YLD', 'Yapı Kredi Altın Fonu', 'Kıymetli Madenler Fonu'],
            ['AGL', 'Ata Portföy Altın Fonu', 'Kıymetli Madenler Fonu'],
            ['GMH', 'Garanti Portföy Gümüş Fonu', 'Kıymetli Madenler Fonu'],
        ],
        'para-piyasasi' => [
            ['PPZ', 'Ziraat Portföy Para Piyasası Fonu', 'Para Piyasası Fonu'],
            ['GPP', 'Garanti Portföy Para Piyasası Fonu', 'Para Piyasası Fonu'],
            ['APP', 'Ak Portföy Para Piyasası Fonu', 'Para Piyasası Fonu'],
            ['TPP', 'TEB Portföy Para Piyasası Fonu', 'Para Piyasası Fonu'],
            ['YPP', 'Yapı Kredi Para Piyasası Fonu', 'Para Piyasası Fonu'],
            ['IPP', 'İş Portföy Para Piyasası Fonu', 'Para Piyasası Fonu'],
        ],
    ];

    /**
     * Sınıf => portföy dağılım şablonu (varlık sınıfı => %).
     */
    private const PORTFOLIO_TEMPLATES = [
        'karma-ve-degisken' => ['Hisse Senedi' => 45, 'Borçlanma Araçları' => 30, 'Ters Repo' => 12, 'Mevduat' => 8, 'Diğer' => 5],
        'borclanma-araclari' => ['Borçlanma Araçları' => 70, 'Ters Repo' => 15, 'Mevduat' => 10, 'Diğer' => 5],
        'fon-sepeti' => ['Yatırım Fonları' => 85, 'Ters Repo' => 8, 'Mevduat' => 5, 'Diğer' => 2],
        'hisse-senedi' => ['Hisse Senedi' => 92, 'Ters Repo' => 5, 'Mevduat' => 2, 'Diğer' => 1],
        'katilim' => ['Katılım Hesapları' => 40, 'Hisse Senedi' => 35, 'Sukuk' => 20, 'Diğer' => 5],
        'kiymetli-madenler' => ['Kıymetli Madenler' => 90, 'Ters Repo' => 6, 'Mevduat' => 3, 'Diğer' => 1],
        'para-piyasasi' => ['Ters Repo' => 55, 'Mevduat' => 30, 'Borçlanma Araçları' => 12, 'Diğer' => 3],
    ];

    public function run(): void
    {
        foreach (self::FUNDS as $class => $funds) {
            foreach ($funds as $i => [$code, $name, $type]) {
                Fund::updateOrCreate(
                    ['code' => $code],
                    $this->row($class, $code, $name, $type, $i)
                );
            }
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function row(string $class, string $code, string $name, string $type, int $sortOrder): array
    {
        $profile = self::CLASS_PROFILE[$class];
        $vol = $profile['vol'];
        [$priceBase, $priceRange] = $profile['price'];

        $price = round($priceBase + $this->unit($code.'price') * $priceRange, 6);
        $changeTl = round($this->signed($code.'tl') * $vol, 2);
        $changeUsd = round($changeTl + $this->signed($code.'usd') * 1.5, 2);

        $risk = $profile['risk'];
        if ($risk < 7 && $this->unit($code.'risk') > 0.7) {
            $risk++;
        }

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'fund_class' => $class,
            'type' => $type,
            'price' => $price,
            'change_tl_percent' => $changeTl,
            'change_usd_percent' => $changeUsd,
            'return_daily' => round($changeTl, 2),
            'return_weekly' => round($this->signed($code.'w') * $vol * 1.8, 2),
            'return_monthly' => round($this->signed($code.'m') * $vol * 3.0, 2),
            'return_quarterly' => round($this->signed($code.'q') * $vol * 5.0, 2),
            'return_semiannual' => round($this->signed($code.'s') * $vol * 8.0, 2),
            'return_yearly' => round($this->signed($code.'y') * $vol * 12.0, 2),
            'return_3y' => round($this->signed($code.'y3') * $vol * 25.0, 2),
            'return_5y' => round($this->signed($code.'y5') * $vol * 40.0, 2),
            'risk_value' => min(7, $risk),
            'investor_count' => 5_000 + (int) ($this->unit($code.'inv') * 895_000),
            'portfolio_value' => 50_000_000 + (int) ($this->unit($code.'pv') * 3_950_000_000),
            'portfolio' => $this->portfolio($class, $code),
            'sort_order' => $sortOrder,
            'is_active' => true,
        ];
    }

    /**
     * Sınıf şablonunu fon bazında küçük oynamalarla dağıtır.
     *
     * @return array<string, float>
     */
    private function portfolio(string $class, string $code): array
    {
        $template = self::PORTFOLIO_TEMPLATES[$class];
        $keys = array_keys($template);
        $dominant = $keys[0];
        $jitter = round($this->signed($code.'pf') * 4, 1);

        $out = [];
        foreach ($template as $asset => $value) {
            $out[$asset] = $asset === $dominant ? round($value + $jitter, 1) : $value;
        }

        return $out;
    }

    /**
     * 0..1 aralığında deterministik sözde-rastgele değer.
     */
    private function unit(string $seed): float
    {
        $x = (crc32($seed) & 0xFFFF) * 2654435761 & 0xFFFFFFFF;
        $x ^= $x >> 15;
        $x = ($x & 0xFFFF) * 2246822519 & 0xFFFFFFFF;
        $x ^= $x >> 13;

        return ($x & 0xFFFFFF) / 0xFFFFFF;
    }

    /**
     * -1..1 aralığında deterministik sözde-rastgele değer.
     */
    private function signed(string $seed): float
    {
        return $this->unit($seed) * 2 - 1;
    }
}