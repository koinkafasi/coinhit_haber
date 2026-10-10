<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BreakingNewsTranslation extends Model
{
    protected $table = 'breaking_news_translations';

    protected $fillable = [
        'breaking_news_id',
        'locale',
        'text',
    ];

    public function breakingNews(): BelongsTo
    {
        return $this->belongsTo(BreakingNews::class);
    }
}
