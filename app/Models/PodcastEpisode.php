<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PodcastEpisode extends Model
{
    /** @use HasFactory<PodcastEpisodeFactory> */
    use HasFactory;

    protected $fillable = [
        'podcast_id',
        'title',
        'slug',
        'description',
        'duration_seconds',
        'audio_url',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'duration_seconds' => 'integer',
    ];

    public function podcast(): BelongsTo
    {
        return $this->belongsTo(Podcast::class);
    }

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
}