<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BroadcastSchedule extends Model
{
    /** @use HasFactory<BroadcastScheduleFactory> */
    use HasFactory;

    public const DAYS = [
        1 => 'Pazartesi',
        2 => 'Salı',
        3 => 'Çarşamba',
        4 => 'Perşembe',
        5 => 'Cuma',
        6 => 'Cumartesi',
        7 => 'Pazar',
    ];

    protected $fillable = [
        'program_id',
        'day_of_week',
        'air_time',
        'program_name',
        'description',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function getDayNameAttribute(): string
    {
        return isset(self::DAYS[$this->day_of_week])
            ? __('media.day_'.$this->day_of_week)
            : '';
    }

    public function getProgramSlugAttribute(): string
    {
        return $this->program?->slug ?? \Illuminate\Support\Str::slug((string) $this->program_name);
    }

    public function getUrlAttribute(): string
    {
        return \App\Support\Locale::url('/tv/program/'.$this->program_slug);
    }
}