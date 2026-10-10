<?php

namespace Database\Factories;

use App\Models\BroadcastSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BroadcastSchedule>
 */
class BroadcastScheduleFactory extends Factory
{
    protected $model = BroadcastSchedule::class;

    public function definition(): array
    {
        return [
            'day_of_week' => fake()->numberBetween(1, 7),
            'air_time' => sprintf('%02d:%02d', fake()->numberBetween(6, 23), fake()->randomElement([0, 10, 15, 30, 45])),
            'program_name' => ucfirst(fake('tr_TR')->words(2, true)),
            'description' => fake('tr_TR')->sentence(8),
        ];
    }
}