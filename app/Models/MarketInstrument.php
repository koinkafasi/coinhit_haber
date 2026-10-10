<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketInstrument extends Model
{
    /** @use HasFactory<MarketInstrumentFactory> */
    use HasFactory;

    public const GROUP_BIST_INDEX = 'bist_index';

    public const GROUP_WORLD_INDEX = 'world_index';

    public const GROUP_STOCK = 'stock';

    public const GROUP_CURRENCY = 'currency';

    public const GROUP_PARITY = 'parity';

    public const GROUP_EMERGING_FX = 'emerging_fx';

    public const GROUP_GOLD = 'gold';

    public const GROUP_PRECIOUS_METAL = 'precious_metal';

    public const GROUP_AGRICULTURE = 'agriculture';

    public const GROUP_INDUSTRY = 'industry';

    public const GROUP_ENERGY = 'energy';

    public const GROUP_BOND_TR = 'bond_tr';

    public const GROUP_REPO = 'repo';

    public const GROUP_EUROBOND = 'eurobond';

    public const GROUP_INTL_BOND = 'intl_bond';

    public const GROUP_ALTERNATIVE = 'alternative';

    public const GROUP_BALTIC = 'baltic';

    public const GROUP_CRYPTO = 'crypto';

    /**
     * Grup görünen adları.
     */
    public const GROUPS = [
        self::GROUP_BIST_INDEX => 'BIST Endeksleri',
        self::GROUP_WORLD_INDEX => 'Dünya Borsaları',
        self::GROUP_STOCK => 'Hisse Senetleri',
        self::GROUP_CURRENCY => 'Döviz',
        self::GROUP_PARITY => 'Pariteler',
        self::GROUP_EMERGING_FX => 'Gelişen Piyasa Kurları',
        self::GROUP_GOLD => 'Altın',
        self::GROUP_PRECIOUS_METAL => 'Kıymetli Metaller',
        self::GROUP_AGRICULTURE => 'Tarım Emtia',
        self::GROUP_INDUSTRY => 'Sanayi Emtia',
        self::GROUP_ENERGY => 'Enerji Emtia',
        self::GROUP_BOND_TR => 'Tahvil-Bono',
        self::GROUP_REPO => 'Repo',
        self::GROUP_EUROBOND => 'Eurobondlar',
        self::GROUP_INTL_BOND => 'Uluslararası Tahviller',
        self::GROUP_ALTERNATIVE => 'Alternatif Piyasalar',
        self::GROUP_BALTIC => 'Baltık',
        self::GROUP_CRYPTO => 'Kripto Para',
    ];

    protected $fillable = [
        'group',
        'code',
        'name',
        'slug',
        'currency',
        'decimals',
        'unit',
        'base_price',
        'previous_close',
        'meta',
        'source',
        'delay_minutes',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'meta' => 'array',
        'decimals' => 'integer',
        'delay_minutes' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'base_price' => 'float',
        'previous_close' => 'float',
    ];

    public function getUrlAttribute(): string
    {
        return '/piyasa/'.$this->slug;
    }

    /**
     * Hisse detay rotası (spec §3): `/borsa/hisse/<kod>-<slug>-detay`.
     */
    public function stockUrl(): string
    {
        return '/borsa/hisse/'.strtolower($this->code).'-'.$this->slug.'-detay';
    }

    /**
     * Endeks detay rotası (spec §3): `/borsa/endeks/<slug>`.
     */
    public function indexUrl(): string
    {
        return '/borsa/endeks/'.$this->slug;
    }

    /**
     * Emtia/Baltık detay rotası (spec §3): `/emtia/<slug>`.
     */
    public function commodityUrl(): string
    {
        return '/emtia/'.$this->slug;
    }
}
