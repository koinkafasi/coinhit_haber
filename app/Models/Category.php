<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory, HasTranslations;

    protected $with = ['translations'];

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

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
            'slug' => 'slug',
            'description' => 'description',
        ];
    }

    public function translationModel(): string
    {
        return CategoryTranslation::class;
    }

    public function translationForeignKey(): string
    {
        return 'category_id';
    }
}
