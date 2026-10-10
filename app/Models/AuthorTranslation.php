<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthorTranslation extends Model
{
    protected $table = 'author_translations';

    protected $fillable = [
        'author_id',
        'locale',
        'name',
        'bio',
        'title',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }
}
