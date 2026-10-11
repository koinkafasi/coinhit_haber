<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection as BaseCollection;

/**
 * CoinGecko'dan çekilen kripto fiyat anlık görüntüsü (CH-2).
 *
 * Her satır bir enstrümanın bir yakalama anındaki piyasa durumudur;
 * `market:snapshot` komutu periyodik olarak yazar, `MarketDataService`
 * taze satırları okuyarak simülatörü gerçek veriyle besler.
 */
class MarketSnapshot extends Model
{
    /** @use HasFactory<MarketSnapshotFactory> */
    use HasFactory;

    protected $fillable = [
        'market_instrument_id',
        'price',
        'market_cap',
        'volume_24h',
        'change_24h',
        'change_7d',
        'change_30d',
        'high_24h',
        'low_24h',
        'sparkline_7d',
        'source',
        'captured_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'float',
            'market_cap' => 'float',
            'volume_24h' => 'float',
            'change_24h' => 'float',
            'change_7d' => 'float',
            'change_30d' => 'float',
            'high_24h' => 'float',
            'low_24h' => 'float',
            'sparkline_7d' => 'array',
            'captured_at' => 'datetime',
        ];
    }

    public function instrument(): BelongsTo
    {
        return $this->belongsTo(MarketInstrument::class, 'market_instrument_id');
    }

    /**
     * Bir grubun (ör. crypto) her enstrümanı için en taze snapshot satırları.
     *
     * @return BaseCollection<int, MarketSnapshot> market_instrument_id => snapshot
     */
    public static function latestForGroup(string $group): BaseCollection
    {
        $latest = self::whereHas('instrument', fn ($q) => $q->where('group', $group)->where('is_active', true))
            ->orderByDesc('captured_at')
            ->orderByDesc('id')
            ->get()
            ->unique('market_instrument_id')
            ->keyBy('market_instrument_id');

        return $latest;
    }
}
