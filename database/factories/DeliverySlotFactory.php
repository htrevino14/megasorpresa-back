<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeliverySlot>
 */
class DeliverySlotFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->numberBetween(8, 18);
        $end = $start + fake()->numberBetween(1, 4);

        return [
            'city_id' => City::factory(),
            'start_time' => sprintf('%02d:00:00', $start),
            'end_time' => sprintf('%02d:00:00', min($end, 22)),
            'additional_cost' => fake()->randomElement([0, 0, 0, 50, 100]),
            'capacity_limit' => fake()->optional(0.6)->numberBetween(5, 30),
        ];
    }
}
