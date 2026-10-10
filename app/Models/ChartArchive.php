<?php

namespace App\Models;

use App\Support\Locale;
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
        return Locale::url('/grafik-arsivi/'.$this->slug.'-'.$this->chart_id);
    }

    /**
     * Kategori etiketi aktif locale'e göre (master spec §18.4). DB'de TR
     * etiketi saklanır; slug üzerinden lang anahtarına eşlenir.
     */
    public function getCategoryLabelAttribute(): string
    {
        $slug = array_search($this->category, \App\Http\Controllers\ChartArchiveController::CATEGORIES, true);

        return $slug !== false ? __('media.chart_category_'.$slug) : (string) $this->category;
    }
}
