<?php

namespace Database\Factories;

use App\Models\MarketInstrument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MarketInstrument>
 */
class MarketInstrumentFactory extends Factory
{
    protected $model = MarketInstrument::class;

    public function definition(): array
    {
        $code = strtoupper(fake()->lexify('?????'));

        return [
            'group' => MarketInstrument::GROUP_CURRENCY,
            'code' => $code,
            'name' => $code.' Kuru',
            'slug' => str($code)->slug().'-kuru',
            'currency' => 'TRY',
            'decimals' => 4,
            'unit' => null,
            'base_price' => fake()->randomFloat(4, 1, 100),
            'previous_close' => fake()->randomFloat(4, 1, 100),
            'meta' => [],
            'source' => 'foreks',
            'delay_minutes' => 15,
            'sort_order' => fake()->numberBetween(0, 50),
            'is_active' => true,
        ];
    }
}