<?php

namespace Database\Factories;

use App\Models\BreakingNews;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BreakingNews>
 */
class BreakingNewsFactory extends Factory
{
    protected $model = BreakingNews::class;

    public function definition(): array
    {
        return [
            'title' => ucfirst(fake('tr_TR')->sentence(8, true)),
            'published_at' => now()->subMinutes(fake()->numberBetween(5, 500)),
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
