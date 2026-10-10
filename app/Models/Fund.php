<?php

namespace App\Models;

use App\Support\Locale;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Yatırım fonu kataloğu (spec §5 fon verisi, §8 `/yatirim-fonlari`).
 */
class Fund extends Model
{
    /** @use HasFactory<FundFactory> */
    use HasFactory;

    /**
     * 7 fon sınıfı (spec §8, §15.8) — sekme sırası birebir.
     */
    public const CLASSES = [
        'karma-ve-degisken' => 'Karma ve Değişken',
        'borclanma-araclari' => 'Borçlanma Araçları',
        'fon-sepeti' => 'Fon Sepeti',
        'hisse-senedi' => 'Hisse Senedi',
        'katilim' => 'Katılım',
        'kiymetli-madenler' => 'Kıymetli Madenler',
        'para-piyasasi' => 'Para Piyasası Fonları',
    ];

    /**
     * Getiri matrisi kolonları (dönemsel, günlük→5 yıllık).
     */
    public const RETURN_COLUMNS = [
        ['field' => 'return_daily', 'label' => 'Günlük(%)'],
        ['field' => 'return_weekly', 'label' => 'Haftalık(%)'],
        ['field' => 'return_monthly', 'label' => 'Aylık(%)'],
        ['field' => 'return_quarterly', 'label' => '3 Aylık(%)'],
        ['field' => 'return_semiannual', 'label' => '6 Aylık(%)'],
        ['field' => 'return_yearly', 'label' => '1 Yıllık(%)'],
        ['field' => 'return_3y', 'label' => '3 Yıllık(%)'],
        ['field' => 'return_5y', 'label' => '5 Yıllık(%)'],
    ];

    protected $fillable = [
        'code',
        'name',
        'slug',
        'fund_class',
        'type',
        'price',
        'change_tl_percent',
        'change_usd_percent',
        'return_daily',
        'return_weekly',
        'return_monthly',
        'return_quarterly',
        'return_semiannual',
        'return_yearly',
        'return_3y',
        'return_5y',
        'risk_value',
        'investor_count',
        'portfolio_value',
        'portfolio',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'price' => 'float',
        'change_tl_percent' => 'float',
        'change_usd_percent' => 'float',
        'return_daily' => 'float',
        'return_weekly' => 'float',
        'return_monthly' => 'float',
        'return_quarterly' => 'float',
        'return_semiannual' => 'float',
        'return_yearly' => 'float',
        'return_3y' => 'float',
        'return_5y' => 'float',
        'risk_value' => 'integer',
        'investor_count' => 'integer',
        'portfolio_value' => 'float',
        'portfolio' => 'array',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Fon detay rotası (spec §3): `/yatirim-fonlari/<fon-slug>`.
     */
    public function getUrlAttribute(): string
    {
        return Locale::url('/yatirim-fonlari/'.$this->slug);
    }

    /**
     * Fon sınıfı görünen adı.
     */
    public function getClassNameAttribute(): string
    {
        return self::CLASSES[$this->fund_class] ?? $this->fund_class;
    }

    /**
     * Risk değeri etiketi (1-7, düşük→yüksek).
     */
    public function getRiskLabelAttribute(): string
    {
        return match (true) {
            $this->risk_value <= 1 => 'Çok Düşük',
            $this->risk_value === 2 => 'Düşük',
            $this->risk_value === 3 => 'Düşük-Orta',
            $this->risk_value === 4 => 'Orta',
            $this->risk_value === 5 => 'Orta-Yüksek',
            $this->risk_value === 6 => 'Yüksek',
            default => 'Çok Yüksek',
        };
    }

    /**
     * Getiri matrisi: alan => değer (günlük→5 yıllık).
     *
     * @return array<string, float>
     */
    public function returnMatrix(): array
    {
        $matrix = [];

        foreach (self::RETURN_COLUMNS as $column) {
            $matrix[$column['field']] = (float) $this->{$column['field']};
        }

        return $matrix;
    }

    /**
     * Portföy dağılımı (yüzde): varlık sınıfı => oran.
     *
     * @return array<string, float>
     */
    public function portfolioDistribution(): array
    {
        $portfolio = (array) ($this->portfolio ?? []);
        $total = array_sum($portfolio);

        if ($total <= 0) {
            return [];
        }

        return $portfolio;
    }

    /**
     * Yön sınıfı (TL değişimine göre).
     */
    public function direction(): string
    {
        return $this->change_tl_percent > 0 ? 'up' : ($this->change_tl_percent < 0 ? 'down' : 'flat');
    }
}