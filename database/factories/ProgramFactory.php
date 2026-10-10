<?php

namespace Database\Factories;

use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    protected $model = Program::class;

    public function definition(): array
    {
        $name = ucfirst(fake('tr_TR')->words(2, true));

        return [
            'name' => $name,
            'slug' => str($name)->slug(),
            'description' => fake('tr_TR')->paragraph(2),
            'thumbnail' => '/img/placeholders/placeholder-'.fake()->numberBetween(1, 8).'.svg',
            'host' => fake('tr_TR')->name(),
            'air_time' => sprintf('%02d:%02d', fake()->numberBetween(6, 22), fake()->randomElement([0, 15, 30, 45])),
            'day_of_week' => fake()->numberBetween(1, 5),
            'is_live' => false,
            'is_series' => false,
            'sort_order' => fake()->numberBetween(0, 40),
            'is_active' => true,
        ];
    }
}