<?php

namespace App\Services\Market;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

/**
 * Döviz Çevirici (spec §6, §8).
 *
 * 22 para birimi arasında kur çevirisi yapar. TRY karşısı kurlar mümkünse
 * `currency` grubundaki canlı enstrümanlardan, aksi halde (BRL/MXN/CZK/PLN/
 * HUF/ZAR/KWD/CNH) deterministik simülasyon tablosundan gelir.
 *
 * Kurlar "1 birim = X TRY" biçimindedir; çapraz kur `from × (fromRate/TRYKur)`
 * oranıyla hesaplanır.
 */
class CurrencyConverter
{
    /**
     * Çevirici para birimi listesi (spec §6 — 22 birim).
     *
     * @var array<string, string>
     */
    public const CURRENCIES = [
        'TRY' => 'Türk Lirası',
        'USD' => 'ABD Doları',
        'EUR' => 'Euro',
        'GBP' => 'İngiliz Sterlini',
        'CHF' => 'İsviçre Frangı',
        'JPY' => 'Japon Yeni',
        'SAR' => 'Suudi Arabistan Riyali',
        'NOK' => 'Norveç Kronu',
        'DKK' => 'Danimarka Kronu',
        'AUD' => 'Avustralya Doları',
        'CAD' => 'Kanada Doları',
        'SEK' => 'İsveç Kronu',
        'BRL' => 'Brezilya Reali',
        'MXN' => 'Meksika Pesosu',
        'CZK' => 'Çek Korunası',
        'PLN' => 'Polonya Zlotisi',
        'HUF' => 'Macar Forinti',
        'RUB' => 'Rus Rublesi',
        'ZAR' => 'Güney Afrika Randı',
        'KWD' => 'Kuveyt Dinarı',
        'CNY' => 'Çin Yuanı',
        'CNH' => 'Çin Yuanı (Offshore)',
    ];

    /**
     * Para birimi → `currency` grubu enstrüman kodu (TRY karşısı).
     *
     * @var array<string, string>
     */
    private const CATALOG_CODES = [
        'USD' => 'USDTRY',
        'EUR' => 'EURTRY',
        'GBP' => 'GBPTRY',
        'CHF' => 'CHFTRY',
        'JPY' => 'JPYTRY',
        'SAR' => 'SARTRY',
        'NOK' => 'NOKTRY',
        'DKK' => 'DKKTRY',
        'AUD' => 'AUDTRY',
        'CAD' => 'CADTRY',
        'SEK' => 'SEKTRY',
        'RUB' => 'RUBTRY',
        'CNY' => 'CNYTRY',
    ];

    /**
     * Kataloğta bulunmayan birimler için "1 birim = X TRY" simülasyon kuru.
     *
     * @var array<string, float>
     */
    private const FALLBACK_TRY_RATES = [
        'TRY' => 1.0,
        'BRL' => 7.90,
        'MXN' => 2.15,
        'CZK' => 1.85,
        'PLN' => 10.65,
        'HUF' => 0.11,
        'ZAR' => 2.35,
        'KWD' => 141.00,
        'CNH' => 5.95,
    ];

    public function __construct(private readonly MarketDataService $market) {}

    /**
     * Tüm çevirici birimleri için "1 birim = X TRY" kuru.
     *
     * @return array<string, float>
     */
    public function ratesToTry(?CarbonInterface $at = null): array
    {
        $rates = ['TRY' => 1.0];

        foreach (array_keys(self::CURRENCIES) as $code) {
            if ($code !== 'TRY') {
                $rates[$code] = $this->rateToTry($code, $at);
            }
        }

        return $rates;
    }

    /**
     * Belirli bir para biriminin TRY karşısındaki kuru.
     */
    public function rateToTry(string $code, ?CarbonInterface $at = null): float
    {
        $code = strtoupper($code);

        if ($code === 'TRY') {
            return 1.0;
        }

        $catalogCode = self::CATALOG_CODES[$code] ?? null;

        if ($catalogCode !== null) {
            $instrument = $this->market->all()->firstWhere('code', $catalogCode);

            if ($instrument !== null) {
                $value = $this->market->quote($instrument, $at)->last;

                // JPY kataloğu "100 JAPON YENİ" kotasyonudur.
                return $code === 'JPY' ? $value / 100 : $value;
            }
        }

        return self::FALLBACK_TRY_RATES[$code] ?? 1.0;
    }

    /**
     * Tutarı bir birimden diğerine çevirir.
     */
    public function convert(float $amount, string $from, string $to, ?CarbonInterface $at = null): float
    {
        $from = strtoupper($from);
        $to = strtoupper($to);

        $rates = $this->ratesToTry($at);
        $fromRate = $rates[$from] ?? 1.0;
        $toRate = $rates[$to] ?? 1.0;

        if ($fromRate <= 0.0 || $toRate <= 0.0) {
            return 0.0;
        }

        return ($amount * $fromRate) / $toRate;
    }

    /**
     * Sonucu para birimine göre TR biçiminde gösterir (TRY 4, JPY 2 ondalık).
     */
    public function format(float $amount, string $code): string
    {
        $decimals = (int) config('market.currency_decimals.'.strtoupper($code), 4);

        return number_format(
            $amount,
            $decimals,
            (string) config('market.format.decimal_separator', ','),
            (string) config('market.format.thousands_separator', '.'),
        );
    }

    /**
     * Görünüm için varsayılan çevirici bağlamı (USD → TRY, tutar 1).
     *
     * @return array<string, mixed>
     */
    public function defaultContext(?CarbonInterface $at = null): array
    {
        $at = $at ? CarbonImmutable::instance($at) : CarbonImmutable::now();
        $from = 'USD';
        $to = 'TRY';
        $amount = 1.0;
        $result = $this->convert($amount, $from, $to, $at);

        $currencies = array_keys(self::CURRENCIES);
        $decimals = [];

        foreach ($currencies as $code) {
            $decimals[$code] = (int) config('market.currency_decimals.'.$code, 4);
        }

        return [
            'currencies' => self::CURRENCIES,
            'rates' => $this->ratesToTry($at),
            'decimals' => $decimals,
            'from' => $from,
            'to' => $to,
            'amount' => $amount,
            'result' => $result,
            'formattedResult' => $this->format($result, $to),
        ];
    }
}
