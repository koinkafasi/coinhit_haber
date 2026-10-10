<?php

namespace Database\Factories;

use App\Models\ChartArchive;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChartArchive>
 */
class ChartArchiveFactory extends Factory
{
    protected $model = ChartArchive::class;

    public function definition(): array
    {
        $title = ucfirst(fake('tr_TR')->sentence(6, true));

        return [
            'chart_id' => fake()->unique()->numberBetween(3790000, 3799999),
            'category' => fake()->randomElement(['Grafik', 'Piyasa', 'Makro Ekonomi', 'Aktüel']),
            'title' => rtrim($title, '.'),
            'slug' => str($title)->slug(),
            'summary' => fake('tr_TR')->paragraph(),
            'published_at' => now()->subDays(fake()->numberBetween(0, 60)),
        ];
    }
}
