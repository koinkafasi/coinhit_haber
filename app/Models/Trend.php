<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Trend extends Model
{
    /** @use HasFactory<TrendFactory> */
    use HasFactory;

    protected $fillable = [
        'label',
        'slug',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function news(): BelongsToMany
    {
        return $this->belongsToMany(News::class, 'news_trend')->withTimestamps();
    }

    public function getUrlAttribute(): string
    {
        return '/trendler/'.$this->slug;
    }
}