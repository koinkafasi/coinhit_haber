<?php

namespace App\Services\Market;

use Carbon\CarbonInterface;
use App\Models\MarketInstrument;

/**
 * Tek bir enstrümanın canlı fiyat anlık görüntüsü (Bölüm 5).
 *
 * Simülatör tarafından üretilir; biçimlendirme (TRY 4 ondalık, JPY 2 ondalık,
 * altın ALIŞ/SATIŞ, kripto piyasa değeri) bu sınıfta kapsüllenir.
 */
class Quote
{
    public function __construct(
        public MarketInstrument $instrument,
        public float $last,
        public float $previousClose,
        public float $change,
        public float $changePercent,
        public float $monthlyPercent,
        public float $yearlyPercent,
        public float $high,
        public float $low,
        public float $open,
        public float $bid,
        public float $ask,
        public int $volumeLot,
        public float $volumeTl,
        public float $volume24h,
        public float $marketCap,
        public float $change7d,
        public CarbonInterface $updatedAt,
    ) {}

    public function code(): string
    {
        return $this->instrument->code;
    }

    public function group(): string
    {
        return $this->instrument->group;
    }

    public function decimals(): int
    {
        return (int) ($this->instrument->decimals ?? 2);
    }

    /**
     * Yön: up / down / flat (Bölüm 17 renk token'ları).
     */
    public function direction(): string
    {
        if ($this->change > 0) {
            return 'up';
        }

        if ($this->change < 0) {
            return 'down';
        }

        return 'flat';
    }

    /**
     * TR biçimiyle sayı (binlik ".", ondalık ",").
     */
    public function format(float $value, ?int $decimals = null): string
    {
        return number_format(
            $value,
            $decimals ?? $this->decimals(),
            (string) config('market.format.decimal_separator', ','),
            (string) config('market.format.thousands_separator', '.'),
        );
    }

    public function formattedLast(): string
    {
        return $this->format($this->last);
    }

    public function formattedChange(): string
    {
        $sign = $this->change > 0 ? '+' : '';

        return $sign.$this->format($this->change);
    }

    public function formattedChangePercent(): string
    {
        $sign = $this->changePercent > 0 ? '+' : '';

        return $sign.$this->format($this->changePercent, 2).'%';
    }

    public function formattedBid(): string
    {
        return $this->format($this->bid);
    }

    public function formattedAsk(): string
    {
        return $this->format($this->ask);
    }

    public function formattedVolumeLot(): string
    {
        return $this->format((float) $this->volumeLot, 0);
    }

    /**
     * Hacim TL biçimi (binlik/kısaltma).
     */
    public function formattedVolumeTl(): string
    {
        return $this->format($this->volumeTl, 0);
    }

    public function formattedVolume24h(): string
    {
        return '$'.$this->format($this->volume24h, 0);
    }

    public function formattedMarketCap(): string
    {
        return '$'.$this->format($this->marketCap, 0);
    }

    /**
     * Altın serbest piyasa satırı için güncelleme saati (HH:MM).
     */
    public function time(): string
    {
        return $this->updatedAt->format('H:i');
    }

    /**
     * Sunrise/kripto için ABD doları fiyatı.
     */
    public function formattedUsdPrice(): string
    {
        return '$'.number_format($this->last, $this->decimals(), '.', ',');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code(),
            'name' => $this->instrument->name,
            'group' => $this->group(),
            'slug' => $this->instrument->slug,
            'currency' => $this->instrument->currency,
            'decimals' => $this->decimals(),
            'unit' => $this->instrument->unit,
            'last' => $this->last,
            'previous_close' => $this->previousClose,
            'change' => $this->change,
            'change_percent' => $this->changePercent,
            'monthly_percent' => $this->monthlyPercent,
            'yearly_percent' => $this->yearlyPercent,
            'high' => $this->high,
            'low' => $this->low,
            'open' => $this->open,
            'bid' => $this->bid,
            'ask' => $this->ask,
            'volume_lot' => $this->volumeLot,
            'volume_tl' => $this->volumeTl,
            'volume_24h' => $this->volume24h,
            'market_cap' => $this->marketCap,
            'change_7d' => $this->change7d,
            'direction' => $this->direction(),
            'source' => $this->instrument->source,
            'delay_minutes' => $this->instrument->delay_minutes,
            'delay_label' => config("market.sources.{$this->instrument->source}.delay_label"),
            'formatted' => [
                'last' => $this->formattedLast(),
                'change' => $this->formattedChange(),
                'change_percent' => $this->formattedChangePercent(),
            ],
            'updated_at' => $this->updatedAt->format((string) config('market.timestamp_format', 'Y/m/d H:i:s')),
        ];
    }
}