<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Author extends Model
{
    /** @use HasFactory<AuthorFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'avatar',
        'title',
        'bio',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function news(): HasMany
    {
        return $this->hasMany(News::class);
    }
}
