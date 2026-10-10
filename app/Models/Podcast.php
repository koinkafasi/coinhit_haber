<?php

namespace App\Models;

use App\Support\Locale;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Podcast extends Model
{
    /** @use HasFactory<PodcastFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'cover',
        'air_days',
        'air_time',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function episodes(): HasMany
    {
        return $this->hasMany(PodcastEpisode::class);
    }

    public function getUrlAttribute(): string
    {
        return Locale::url('/podcastler/'.$this->slug);
    }
}
