<?php

namespace Database\Factories;

use App\Models\Podcast;
use App\Models\PodcastEpisode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PodcastEpisode>
 */
class PodcastEpisodeFactory extends Factory
{
    protected $model = PodcastEpisode::class;

    public function definition(): array
    {
        return [
            'podcast_id' => Podcast::factory(),
            'title' => ucfirst(fake('tr_TR')->sentence(5, true)),
            'slug' => fake()->unique()->slug(3),
            'description' => fake('tr_TR')->paragraph(2),
            'duration_seconds' => fake()->numberBetween(600, 5400),
            'audio_url' => 'https://podcast.bloomberght.com/demo/'.fake()->uuid().'.mp3',
            'published_at' => now()->subDays(fake()->numberBetween(1, 30)),
        ];
    }
}