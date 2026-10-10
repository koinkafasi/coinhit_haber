<?php

namespace App\Services\Market;

/**
 * Altın Hesaplama (spec §6, §8).
 *
 * Alış/Satış modunda 18 enstrüman (Türk Lirası + 17 altın çeşidi) arasında
 * dönüşüm yapar. Her enstrümanın 1 birim değeri TRY cinsine indirgenir;
 * ALTIN/ONS ($) USD kotasyonu olduğundan USD/TRY kuru ile çarpılır. Böylece
 * ons → gram (≈31,1 gr) ve gram → sepet dönüşümleri doğru sonuç verir.
 */
class GoldCalculator
{
    /**
     * Hesaplayıcı enstrümanları (spec §6 — 18 seçenek, sıra birebir).
     *
     * @var array<string, string>
     */
    public const OPTIONS = [
        'TRY' => 'TÜRK LİRASI',
        'GAU' => 'ALTIN/ONS ($)',
        'GRAM' => 'GRAM ALTIN TL/GR',
        'CUMHUR' => 'CUMHURİYET ALTINI',
        'TAM' => 'TAM ALTIN',
        'ZIYNET' => 'ZİYNET ALTINI',
        'CEYREK' => 'ÇEYREK ALTIN',
        'YARIM' => 'YARIM ALTIN',
        'ATA' => 'ATA ALTIN',
        'BILEZIK14' => '14 AYAR BİLEZİK GRAMI',
        'BILEZIK22' => '22 AYAR BİLEZİK GRAMI',
        'GREMSE' => 'GREMSE ALTIN',
        'GAUTRY' => 'ALTIN/ONS SPOT (TL)',
        'HAS' => 'HAS ALTIN',
        'IKIBUCUK' => 'İKİBUÇUK ALTIN',
        'BESLI' => 'BEŞLİ ALTIN',
        'KULCE_EUR' => 'KÜLÇE EURO',
        'KULCE_USD' => 'KÜLÇE DOLAR',
    ];

    /**
     * @var array<string, string>
     */
    public const MODES = [
        'alis' => 'Alış',
        'satis' => 'Satış',
    ];

    public function __construct(private readonly MarketDataService $market) {}

    /**
     * Görünüm için varsayılan hesaplayıcı bağlamı.
     *
     * @return array<string, mixed>
     */
    public function defaultContext(): array
    {
        $from = 'TRY';
        $to = 'GAU';
        $mode = 'alis';
        $amount = 1.0;
        $result = $this->convert($amount, $from, $to, $mode);

        return [
            'options' => self::OPTIONS,
            'modes' => self::MODES,
            'from' => $from,
            'to' => $to,
            'mode' => $mode,
            'amount' => $amount,
            'result' => $result,
            'values' => $this->values(),
        ];
    }

    /**
     * Her enstrümanın Alış/Satış modunda TRY karşılığı (istemci tarafı hesap).
     *
     * @return array{alis: array<string, float>, satis: array<string, float>}
     */
    public function values(): array
    {
        $out = ['alis' => [], 'satis' => []];

        foreach (array_keys(self::OPTIONS) as $key) {
            $out['alis'][$key] = $this->unitValue($key, 'alis');
            $out['satis'][$key] = $this->unitValue($key, 'satis');
        }

        return $out;
    }

    /**
     * Tutarı bir altın enstrümanından diğerine çevirir.
     */
    public function convert(float $amount, string $from, string $to, string $mode = 'alis'): float
    {
        $fromValue = $this->unitValue($from, $mode);
        $toValue = $this->unitValue($to, $mode);

        if ($toValue <= 0.0) {
            return 0.0;
        }

        return ($amount * $fromValue) / $toValue;
    }

    /**
     * 1 birim enstrümanın TRY değeri (Alış → bid, Satış → ask).
     */
    public function unitValue(string $key, string $mode = 'alis'): float
    {
        $key = strtoupper($key);

        if ($key === 'TRY') {
            return 1.0;
        }

        $quote = $this->quote($key);

        if ($quote === null) {
            return 0.0;
        }

        $value = $mode === 'satis' ? $quote->ask : $quote->bid;

        if ($key === 'GAU') {
            return $value * $this->usdTry();
        }

        return $value;
    }

    private function quote(string $code): ?Quote
    {
        $instrument = $this->market->all()->firstWhere('code', $code);

        return $instrument !== null ? $this->market->quote($instrument) : null;
    }

    private function usdTry(): float
    {
        return $this->quote('USDTRY')?->last ?? 1.0;
    }
}
