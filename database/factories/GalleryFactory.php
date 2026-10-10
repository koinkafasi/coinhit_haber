<?php

namespace Database\Factories;

use App\Models\Gallery;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Gallery>
 */
class GalleryFactory extends Factory
{
    protected $model = Gallery::class;

    public function definition(): array
    {
        $title = ucfirst(fake('tr_TR')->sentence(6, true));

        return [
            'title' => rtrim($title, '.'),
            'slug' => str($title)->slug(),
            'summary' => fake('tr_TR')->paragraph(2),
            'cover' => '/img/placeholders/placeholder-'.fake()->numberBetween(1, 8).'.svg',
            'photo_total_count' => fake()->numberBetween(4, 12),
            'published_at' => now()->subHours(fake()->numberBetween(1, 72)),
        ];
    }
}