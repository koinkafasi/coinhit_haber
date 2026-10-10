<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChartArchive extends Model
{
    /** @use HasFactory<ChartArchiveFactory> */
    use HasFactory;

    protected $fillable = [
        'chart_id',
        'category',
        'title',
        'slug',
        'image',
        'summary',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function getUrlAttribute(): string
    {
        return '/grafik-arsivi/' . $this->slug . '-' . $this->chart_id;
    }
}
