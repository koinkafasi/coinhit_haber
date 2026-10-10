<?php

namespace Database\Factories;

use App\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Author>
 */
class AuthorFactory extends Factory
{
    protected $model = Author::class;

    public function definition(): array
    {
        return [
            'name' => fake('tr_TR')->name(),
            'slug' => fake()->unique()->slug(2),
            'title' => fake('tr_TR')->jobTitle(),
            'bio' => fake('tr_TR')->paragraph(2),
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
