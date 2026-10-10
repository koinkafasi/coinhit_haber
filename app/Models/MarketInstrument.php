<?php

namespace App\Models;

use App\Support\Locale;
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
    /**
     * Döviz kuru detay rotası okunur slug'ları (spec §3, §8).
     *
     * Gerçek sitede detay slug'ları kısaltılmıştır (`abd-dolari` → `dolar`).
     */
    public const CURRENCY_ROUTE_SLUGS = [
        'USDTRY' => 'dolar',
        'EURTRY' => 'euro',
        'GBPTRY' => 'ingiliz-sterlini',
        'CHFTRY' => 'isvicre-frangi',
        'JPYTRY' => 'japon-yeni',
        'SARTRY' => 'suudi-arabistan-riyali',
        'NOKTRY' => 'norvec-kronu',
        'DKKTRY' => 'danimarka-kronu',
        'AUDTRY' => 'avustralya-dolari',
        'CADTRY' => 'kanada-dolari',
        'SEKTRY' => 'isvec-kronu',
        'RUBTRY' => 'rus-rublesi',
        'CNYTRY' => 'cin-yuani',
        'AEDTRY' => 'bae-dirhemi',
    ];

    /**
     * Detay rotası slug → enstrüman kodu eşlemesi (takma adlar dahil).
     */
    public const CURRENCY_ROUTE_ALIASES = [
        'dolar' => 'USDTRY',
        'abd-dolari' => 'USDTRY',
        'euro' => 'EURTRY',
        'sterlin' => 'GBPTRY',
        'ingiliz-sterlini' => 'GBPTRY',
        'frank' => 'CHFTRY',
        'isvicre-frangi' => 'CHFTRY',
        'japon-yeni' => 'JPYTRY',
        '100-japon-yeni' => 'JPYTRY',
        'yen' => 'JPYTRY',
        'suudi-arabistan-riyali' => 'SARTRY',
        'norvec-kronu' => 'NOKTRY',
        'danimarka-kronu' => 'DKKTRY',
        'avustralya-dolari' => 'AUDTRY',
        'kanada-dolari' => 'CADTRY',
        'isvec-kronu' => 'SEKTRY',
        'rus-rublesi' => 'RUBTRY',
        'cin-yuani' => 'CNYTRY',
        'bae-dirhemi' => 'AEDTRY',
    ];

    /**
     * Altın çeşitleri detay rotası okunur slug'ları (spec §3, §8).
     *
     * Gerçek sitede `14 AYAR BİLEZİK` → `14-ay-bilezik-gr`, `ALTIN/ONS (TL)`
     * → `altin-ons-tl` gibi kısaltmalar kullanılır.
     */
    public const GOLD_ROUTE_SLUGS = [
        'GAU' => 'altin-ons',
        'GRAM' => 'gram-altin',
        'GAUTRY' => 'altin-ons-tl',
        'CUMHUR' => 'cumhuriyet-altini',
        'TAM' => 'tam-altin',
        'ZIYNET' => 'ziynet-altini',
        'CEYREK' => 'ceyrek-altin',
        'YARIM' => 'yarim-altin',
        'ATA' => 'ata-altin',
        'BILEZIK14' => '14-ay-bilezik-gr',
        'BILEZIK22' => '22-ay-bilezik-gr',
        'GREMSE' => 'gremse-altin',
        'HAS' => 'has-altin',
        'IKIBUCUK' => 'ikibucuk-altin',
        'BESLI' => 'besli-altin',
        'KULCE_EUR' => 'kulce-euro',
        'KULCE_USD' => 'kulce-dolar',
    ];

    /**
     * Emtia detay rotası okunur slug'ları (spec §3 — Sanayi Emtia 3 Aylık).
     */
    public const COMMODITY_ROUTE_SLUGS = [
        'BAKIR3A' => 'bakir3m',
        'ALUMINYUM3A' => 'aluminyum3m',
        'CINKO3A' => 'cinko3m',
        'NIKEL3A' => 'nikel3m',
        'KALAY3A' => 'kalay3m',
        'KURSUN3A' => 'kursun3m',
    ];

    /**
     * Emtia detay rotası takma adları (spec §3 — sitemap varyantları).
     */
    public const COMMODITY_ROUTE_ALIASES = [
        'petrol' => 'BRENT',
        'bity' => 'BDIY',
        'bidy' => 'BDIY',
        'bdiy' => 'BDIY',
    ];

    /**
     * Tahvil (TR) detay rotası okunur slug'ları (spec §3, §8).
     */
    public const BOND_TR_ROUTE_SLUGS = [
        'TR2Y' => 'tr-2-yillik-tahvil',
        'TR5Y' => 'tr-5-yillik-tahvil',
        'TR10Y' => 'tr-10-yillik-tahvil',
    ];

    /**
     * Uluslararası tahvil detay rotası okunur slug'ları (spec §3, §8).
     */
    public const INTL_BOND_ROUTE_SLUGS = [
        'US2Y' => 'abd-2-yillik-tahvil',
        'US5Y' => 'abd-5-yillik-tahvil',
        'US10Y' => 'abd-10-yillik-tahvil',
        'DE2Y' => 'almanya-2-yillik-tahvil',
        'DE5Y' => 'almanya-5-yillik-tahvil',
        'DE10Y' => 'almanya-10-yillik-tahvil',
        'GB2Y' => 'ingiltere-2-yillik-tahvil',
        'GB5Y' => 'ingiltere-5-yillik-tahvil',
        'GB10Y' => 'ingiltere-10-yillik-tahvil',
        'JP2Y' => 'japonya-2-yillik-tahvil',
        'JP5Y' => 'japonya-5-yillik-tahvil',
        'JP10Y' => 'japonya-10-yillik-tahvil',
    ];

    /**
     * Uluslararası tahvil rotası takma adları (spec §5 — sitemap `almayna` TYPO'su).
     */
    public const INTL_BOND_ROUTE_ALIASES = [
        'almayna-2-yillik-tahvil' => 'DE2Y',
        'almayna-5-yillik-tahvil' => 'DE5Y',
        'almayna-10-yillik-tahvil' => 'DE10Y',
    ];

    /**
     * Repo detay rotası okunur slug'ları (spec §3).
     */
    public const REPO_ROUTE_SLUGS = [
        'REPO_GECELIK' => 'repo-gecelik',
        'REPO_HAFTALIK' => 'repo-haftalik',
        'REPO_AYLIK' => 'repo-aylik',
    ];

    /**
     * Eurobond detay rotası okunur slug'ları (spec §3): `/eurobond/tr-eurobond-{n}-yil`.
     */
    public const EUROBOND_ROUTE_SLUGS = [
        'EUROBOND_TR_1Y' => 'tr-eurobond-1-yil',
        'EUROBOND_TR_2Y' => 'tr-eurobond-2-yil',
        'EUROBOND_TR_3Y' => 'tr-eurobond-3-yil',
        'EUROBOND_TR_4Y' => 'tr-eurobond-4-yil',
        'EUROBOND_TR_5Y' => 'tr-eurobond-5-yil',
        'EUROBOND_TR_6Y' => 'tr-eurobond-6-yil',
        'EUROBOND_TR_7Y' => 'tr-eurobond-7-yil',
        'EUROBOND_TR_8Y' => 'tr-eurobond-8-yil',
        'EUROBOND_TR_10Y' => 'tr-eurobond-10-yil',
        'EUROBOND_TR_20Y' => 'tr-eurobond-20-yil',
        'EUROBOND_TR_30Y' => 'tr-eurobond-30-yil',
    ];

    /**
     * Kripto detay rotası okunur slug'ları (spec §3): yalnızca BTC/ETH/XRP.
     */
    public const CRYPTO_ROUTE_SLUGS = [
        'BTC' => 'bitcoin',
        'ETH' => 'ethereum',
        'XRP' => 'ripple',
    ];

    /**
     * Kripto detay rotası takma adları (`/doviz/<slug>`).
     */
    public const CRYPTO_ROUTE_ALIASES = [
        'bitcoin' => 'BTC',
        'ethereum' => 'ETH',
        'ripple' => 'XRP',
    ];

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
        return Locale::url('/piyasa/'.$this->slug);
    }

    /**
     * Hisse detay rotası (spec §3): `/borsa/hisse/<kod>-<slug>-detay`.
     */
    public function stockUrl(): string
    {
        return Locale::url('/borsa/hisse/'.strtolower($this->code).'-'.$this->slug.'-detay');
    }

    /**
     * Endeks detay rotası (spec §3): `/borsa/endeks/<slug>`.
     */
    public function indexUrl(): string
    {
        return Locale::url('/borsa/endeks/'.$this->slug);
    }

    /**
     * Döviz kuru detay rotası (spec §3, §8): `/doviz/<slug>`.
     */
    public function currencyUrl(): string
    {
        return Locale::url('/doviz/'.(self::CURRENCY_ROUTE_SLUGS[$this->code] ?? $this->slug));
    }

    /**
     * Parite detay rotası (spec §3, §8): `/doviz/parite/<slug>`.
     */
    public function parityUrl(): string
    {
        return Locale::url('/doviz/parite/'.$this->parityRouteSlug());
    }

    /**
     * Parite detay okunur slug'ı (spec §3: `eur-usd`, `dolar-endeksi`).
     */
    public function parityRouteSlug(): string
    {
        if ($this->code === 'DXY') {
            return 'dolar-endeksi';
        }

        $code = strtolower($this->code);

        return strlen($code) === 6 ? substr($code, 0, 3).'-'.substr($code, 3, 3) : $code;
    }

    /**
     * Altın detay rotası (spec §3, §8): `/altin/<slug>`.
     */
    public function goldUrl(): string
    {
        return Locale::url('/altin/'.(self::GOLD_ROUTE_SLUGS[$this->code] ?? $this->slug));
    }

    /**
     * Altın detay rotası slug'ı (kataloğa göre).
     */
    public function goldRouteSlug(): string
    {
        return self::GOLD_ROUTE_SLUGS[$this->code] ?? $this->slug;
    }

    /**
     * Emtia/Baltık detay rotası (spec §3): `/emtia/<slug>`.
     */
    public function commodityUrl(): string
    {
        return Locale::url('/emtia/'.(self::COMMODITY_ROUTE_SLUGS[$this->code] ?? $this->slug));
    }

    /**
     * Emtia detay rotası slug'ı (kataloğa göre).
     */
    public function commodityRouteSlug(): string
    {
        return self::COMMODITY_ROUTE_SLUGS[$this->code] ?? $this->slug;
    }

    /**
     * Tahvil detay rotası (spec §3, §8): `/tahvil/<slug>`.
     *
     * TR tahvil ve uluslararası tahvil aynı rotayı paylaşır.
     */
    public function bondUrl(): string
    {
        return Locale::url('/tahvil/'.$this->bondRouteSlug());
    }

    /**
     * Tahvil detay rotası slug'ı (TR + uluslararası).
     */
    public function bondRouteSlug(): string
    {
        $map = $this->group === self::GROUP_INTL_BOND
            ? self::INTL_BOND_ROUTE_SLUGS
            : self::BOND_TR_ROUTE_SLUGS;

        return $map[$this->code] ?? $this->slug;
    }

    /**
     * Repo detay rotası (spec §3): `/repo/<slug>`.
     */
    public function repoUrl(): string
    {
        return Locale::url('/repo/'.(self::REPO_ROUTE_SLUGS[$this->code] ?? $this->slug));
    }

    /**
     * Eurobond detay rotası (spec §3): `/eurobond/<slug>`.
     */
    public function eurobondUrl(): string
    {
        return Locale::url('/eurobond/'.(self::EUROBOND_ROUTE_SLUGS[$this->code] ?? $this->slug));
    }

    /**
     * Kripto detay rotası (spec §3): yalnızca BTC/ETH/XRP → `/doviz/<slug>`,
     * diğerleri için null (metin satırı).
     */
    public function cryptoUrl(): ?string
    {
        $slug = self::CRYPTO_ROUTE_SLUGS[$this->code] ?? null;

        return $slug !== null ? Locale::url('/doviz/'.$slug) : null;
    }
}
