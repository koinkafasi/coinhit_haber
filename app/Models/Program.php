<?php

namespace App\Models;

use App\Support\Locale;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    /** @use HasFactory<ProgramFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'thumbnail',
        'host',
        'air_time',
        'day_of_week',
        'is_live',
        'is_series',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_live' => 'boolean',
        'is_series' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function episodes(): HasMany
    {
        return $this->hasMany(ProgramEpisode::class);
    }

    /**
     * Yalnız video serileri (Bölüm 8 /video).
     */
    public function scopeSeries($query)
    {
        return $query->where('is_series', true);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(BroadcastSchedule::class);
    }

    public function getUrlAttribute(): string
    {
        return Locale::url('/tv/program/'.$this->slug);
    }
}
