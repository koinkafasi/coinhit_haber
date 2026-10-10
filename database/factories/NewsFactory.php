<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\News;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<News>
 */
class NewsFactory extends Factory
{
    protected $model = News::class;

    public function definition(): array
    {
        $title = ucfirst(fake('tr_TR')->sentence(7, true));

        return [
            'news_id' => fake()->unique()->numberBetween(3790000, 3799999),
            'category_id' => Category::factory(),
            'title' => rtrim($title, '.'),
            'slug' => str($title)->slug(),
            'spot' => fake('tr_TR')->paragraph(2),
            'body' => fake('tr_TR')->paragraphs(5, true),
            'image' => '/img/placeholders/placeholder-'.fake()->numberBetween(1, 8).'.svg',
            'image_og' => '/img/og-default.png',
            'source' => 'Bloomberg HT',
            'tags' => [fake('tr_TR')->word()],
            'status' => 'published',
            'is_breaking' => false,
            'is_featured' => fake()->boolean(20),
            'views' => fake()->numberBetween(0, 500000),
            'editor_id' => 1,
            'published_at' => now()->subHours(fake()->numberBetween(1, 48)),
            'modified_at' => now(),
        ];
    }

    public function breaking(): static
    {
        return $this->state(fn () => ['is_breaking' => true]);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }
}
