<?php

namespace Database\Factories;

use App\Models\Advertisement;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Advertisement>
 */
class AdvertisementFactory extends Factory
{
    protected $model = Advertisement::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'position' => '1257',
            'image_path' => '/gorsel/ads/'.Str::random(20).'.jpg',
            'link_url' => 'https://example.com/',
            'advertiser_name' => fake()->company(),
            'code' => null,
            'starts_at' => null,
            'ends_at' => null,
            'is_active' => true,
        ];
    }
}
