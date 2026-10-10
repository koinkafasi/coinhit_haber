<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BreakingNews extends Model
{
    /** @use HasFactory<BreakingNewsFactory> */
    use HasFactory;

    protected $fillable = [
        'news_id',
        'title',
        'short_code',
        'published_at',
        'date_group',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'date_group' => 'date',
        'is_active' => 'boolean',
    ];

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }

    /**
     * Kısa link (spec §4 — `bb.ht/{kod}`).
     */
    public function getShortlinkAttribute(): string
    {
        return $this->short_code ? 'bb.ht/'.$this->short_code : '';
    }
}