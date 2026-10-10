<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class News extends Model
{
    /** @use HasFactory<NewsFactory> */
    use HasFactory;

    protected $fillable = [
        'news_id',
        'category_id',
        'author_id',
        'title',
        'slug',
        'spot',
        'body',
        'image',
        'image_og',
        'source',
        'tags',
        'status',
        'is_breaking',
        'is_featured',
        'views',
        'editor_id',
        'published_at',
        'modified_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'status' => 'string',
        'is_breaking' => 'boolean',
        'is_featured' => 'boolean',
        'views' => 'integer',
        'published_at' => 'datetime',
        'modified_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function getUrlAttribute(): string
    {
        return '/' . $this->slug . '-' . $this->news_id;
    }
}
