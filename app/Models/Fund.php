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

    /**
     * Aktif locale'e göre fon sınıfı etiketleri (master spec §18.4).
     *
     * @return array<string, string>
     */
    public static function classLabels(): array
    {
        $labels = [];

        foreach (self::CLASSES as $key => $label) {
            $labels[$key] = __('media.fund_class_'.$key);
        }

        return $labels;
    }

    /**
     * Aktif locale'e göre getiri matrisi kolonları.
     *
     * @return array<int, array{field: string, label: string}>
     */
    public static function returnColumns(): array
    {
        return array_map(static fn (array $column): array => [
            'field' => $column['field'],
            'label' => __('media.'.$column['field']),
        ], self::RETURN_COLUMNS);
    }

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
        return isset(self::CLASSES[$this->fund_class])
            ? __('media.fund_class_'.$this->fund_class)
            : $this->fund_class;
    }

    /**
     * Risk değeri etiketi (1-7, düşük→yüksek).
     */
    public function getRiskLabelAttribute(): string
    {
        $level = match (true) {
            $this->risk_value <= 1 => 1,
            $this->risk_value === 2 => 2,
            $this->risk_value === 3 => 3,
            $this->risk_value === 4 => 4,
            $this->risk_value === 5 => 5,
            $this->risk_value === 6 => 6,
            default => 7,
        };

        return __('media.risk_'.$level);
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