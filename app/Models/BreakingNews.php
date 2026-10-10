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
        'published_at',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function news(): BelongsTo
    {
        return $this->belongsTo(News::class);
    }
}
