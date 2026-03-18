<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reminder>
 */
class ReminderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'event_name' => fake()->randomElement([
                'Cumpleaños de mamá',
                'Cumpleaños de papá',
                'Aniversario',
                'Navidad',
                'Día del niño',
                'Graduación',
                'Baby shower',
                'Posada',
            ]),
            'date' => fake()->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'notify_days_before' => fake()->randomElement([1, 3, 5, 7, 14, 30]),
        ];
    }
}
