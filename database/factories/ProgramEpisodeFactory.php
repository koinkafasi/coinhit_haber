<?php

namespace Database\Factories;

use App\Models\Program;
use App\Models\ProgramEpisode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProgramEpisode>
 */
class ProgramEpisodeFactory extends Factory
{
    protected $model = ProgramEpisode::class;

    public function definition(): array
    {
        return [
            'program_id' => Program::factory(),
            'title' => ucfirst(fake('tr_TR')->sentence(5, true)),
            'slug' => fake()->unique()->slug(3),
            'guest' => fake('tr_TR')->name(),
            'duration_seconds' => fake()->numberBetween(180, 7200),
            'thumbnail' => '/img/placeholders/placeholder-'.fake()->numberBetween(1, 8).'.svg',
            'video_url' => 'https://vmcdn.ciner.com.tr/demo/'.fake()->uuid().'.mp4',
            'air_at' => now()->subHours(fake()->numberBetween(1, 96)),
        ];
    }
}