<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Author extends Model
{
    /** @use HasFactory<AuthorFactory> */
    use HasFactory, HasTranslations;

    protected $with = ['translations'];

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

    /**
     * @return array<string, string>
     */
    public function translatable(): array
    {
        return [
            'name' => 'name',
            'bio' => 'bio',
            'title' => 'title',
        ];
    }

    public function translationModel(): string
    {
        return AuthorTranslation::class;
    }

    public function translationForeignKey(): string
    {
        return 'author_id';
    }
}
