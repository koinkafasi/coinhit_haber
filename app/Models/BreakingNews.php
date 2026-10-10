<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BreakingNews extends Model
{
    /** @use HasFactory<BreakingNewsFactory> */
    use HasFactory, HasTranslations;

    protected $with = ['translations'];

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
     * @return array<string, string>
     */
    public function translatable(): array
    {
        return [
            'title' => 'text',
        ];
    }

    public function translationModel(): string
    {
        return BreakingNewsTranslation::class;
    }

    public function translationForeignKey(): string
    {
        return 'breaking_news_id';
    }

    /**
     * Kısa link (spec §4 — `bb.ht/{kod}`).
     */
    public function getShortlinkAttribute(): string
    {
        return $this->short_code ? 'bb.ht/'.$this->short_code : '';
    }
}
