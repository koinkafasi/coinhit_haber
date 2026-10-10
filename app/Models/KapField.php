<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KapField extends Model
{
    /** @use HasFactory<KapFieldFactory> */
    use HasFactory;

    protected $fillable = [
        'kap_disclosure_id',
        'key',
        'label_tr',
        'label_en',
        'value_tr',
        'value_en',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function disclosure(): BelongsTo
    {
        return $this->belongsTo(KapDisclosure::class, 'kap_disclosure_id');
    }
}