<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpoCalendar extends Model
{
    /** @use HasFactory<IpoCalendarFactory> */
    use HasFactory;

    protected $fillable = [
        'company_name',
        'ticker',
        'request_start_date',
        'request_end_date',
        'lot_value',
        'price',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'lot_value' => 'decimal:2',
        'price' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    /**
     * Talep toplama tarih aralığı (spec §4/§8 — "Talep Tarihleri").
     */
    public function getRequestRangeAttribute(): string
    {
        if ($this->request_start_date && $this->request_end_date) {
            return $this->request_start_date.' - '.$this->request_end_date;
        }

        return (string) ($this->request_start_date ?? $this->request_end_date ?? '');
    }
}