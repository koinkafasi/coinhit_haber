<?php

namespace Database\Factories;

use App\Models\Podcast;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Podcast>
 */
class PodcastFactory extends Factory
{
    protected $model = Podcast::class;

    public function definition(): array
    {
        $title = ucfirst(fake('tr_TR')->words(2, true));

        return [
            'title' => $title,
            'slug' => str($title)->slug(),
            'description' => fake('tr_TR')->paragraph(2),
            'cover' => '/img/placeholders/placeholder-'.fake()->numberBetween(1, 8).'.svg',
            'air_days' => 'Her Cuma',
            'air_time' => '18:00',
            'sort_order' => fake()->numberBetween(0, 20),
            'is_active' => true,
        ];
    }
}