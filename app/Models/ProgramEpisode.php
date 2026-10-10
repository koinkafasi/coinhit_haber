<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramEpisode extends Model
{
    /** @use HasFactory<ProgramEpisodeFactory> */
    use HasFactory;

    protected $fillable = [
        'program_id',
        'title',
        'slug',
        'guest',
        'duration_seconds',
        'thumbnail',
        'video_url',
        'air_at',
    ];

    protected $casts = [
        'air_at' => 'datetime',
        'duration_seconds' => 'integer',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Süreyi HH:MM:SS veya M:SS olarak biçimler (spec §4).
     */
    public function getDurationAttribute(): string
    {
        $seconds = max(0, (int) $this->duration_seconds);
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
        }

        return sprintf('%d:%02d', $minutes, $secs);
    }

    /**
     * "{{Şov}} - {{Konuk}} | {{tarih}}" başlık deseni (spec §4).
     */
    public function getDisplayTitleAttribute(): string
    {
        $title = (string) $this->title;

        if ($this->program && ! str_contains($title, (string) $this->program->name)) {
            $title = $this->program->name.' - '.$title;
        }

        if ($this->guest) {
            $title .= ' - '.$this->guest;
        }

        if ($this->air_at) {
            $title .= ' | '.$this->air_at->format('d.m.Y');
        }

        return $title;
    }
}