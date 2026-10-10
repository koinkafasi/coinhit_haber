<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gallery extends Model
{
    /** @use HasFactory<GalleryFactory> */
    use HasFactory;

    protected $fillable = [
        'news_id',
        'category_id',
        'title',
        'slug',
        'summary',
        'cover',
        'photo_total_count',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'photo_total_count' => 'integer',
    ];

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(GalleryItem::class)->orderBy('sort_order');
    }

    public function getUrlAttribute(): string
    {
        return '/fotohaber/'.$this->slug.'-'.$this->id;
    }
}