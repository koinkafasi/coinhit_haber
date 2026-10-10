<?php

namespace Database\Factories;

use App\Models\Trend;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Trend>
 */
class TrendFactory extends Factory
{
    protected $model = Trend::class;

    public function definition(): array
    {
        $label = ucfirst(fake('tr_TR')->word());

        return [
            'label' => $label,
            'slug' => str($label)->slug(),
            'description' => fake('tr_TR')->sentence(10),
            'sort_order' => fake()->numberBetween(0, 20),
            'is_active' => true,
        ];
    }
}