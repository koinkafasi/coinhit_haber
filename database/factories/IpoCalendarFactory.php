<?php

namespace Database\Factories;

use App\Models\IpoCalendar;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IpoCalendar>
 */
class IpoCalendarFactory extends Factory
{
    protected $model = IpoCalendar::class;

    public function definition(): array
    {
        $start = now()->addDays(fake()->numberBetween(1, 20));

        return [
            'company_name' => fake('tr_TR')->company(),
            'ticker' => strtoupper(fake()->lexify('????')),
            'request_start_date' => $start->format('d.m.Y'),
            'request_end_date' => $start->copy()->addDays(2)->format('d.m.Y'),
            'lot_value' => fake()->numberBetween(100000, 50000000),
            'price' => fake()->randomFloat(2, 5, 120),
            'status' => fake()->randomElement(['planned', 'active', 'completed']),
            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }
}