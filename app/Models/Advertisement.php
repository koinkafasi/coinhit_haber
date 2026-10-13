<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Yönetilebilir reklam — edirnegercek projesindeki Advertisement yaklaşımıyla
 * birebir: her reklam bir konuma (zone id) bağlıdır; aktifse görüntü/bağlantı
 * (veya ham kod) ilgili yuvada gösterilir, değilse alan kapatılır.
 */
class Advertisement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'position',
        'image_path',
        'link_url',
        'advertiser_name',
        'code',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Talep içi bellek — aynı zon defalarca sorgulanmasın.
     *
     * @var array<string, Advertisement|null>
     */
    protected static array $memo = [];

    public function scopeActiveInPosition($query, ?string $position)
    {
        return $query->where('position', $position)
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()->toDateString()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()->toDateString()));
    }

    /**
     * Belirli bir zone için geçerli aktif reklamı döndürür (yoksa null).
     */
    public static function forZone(?string $zone): ?self
    {
        if ($zone === null || $zone === '') {
            return null;
        }

        return static::$memo[$zone] ??= static::activeInPosition($zone)->orderBy('starts_at')->first();
    }

    /**
     * Talep içi belleği temizler (testlerde kullanılır).
     */
    public static function flushMemo(): void
    {
        static::$memo = [];
    }

    /**
     * @return array<string, string>  zone id => açıklama
     */
    public static function zones(): array
    {
        return (array) config('ads.zones', []);
    }

    public function zoneLabel(): ?string
    {
        return static::zones()[$this->position] ?? null;
    }

    public function imageUrl(): ?string
    {
        if (! $this->image_path) {
            return null;
        }

        return str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')
            ? $this->image_path
            : url($this->image_path);
    }
}
