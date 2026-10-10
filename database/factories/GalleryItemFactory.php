<?php

namespace Database\Factories;

use App\Models\Gallery;
use App\Models\GalleryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GalleryItem>
 */
class GalleryItemFactory extends Factory
{
    protected $model = GalleryItem::class;

    public function definition(): array
    {
        return [
            'gallery_id' => Gallery::factory(),
            'image' => '/img/placeholders/placeholder-'.fake()->numberBetween(1, 8).'.svg',
            'caption' => fake('tr_TR')->sentence(8),
            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }
}