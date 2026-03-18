<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AnnouncementBar>
 */
class AnnouncementBarFactory extends Factory
{
    public function definition(): array
    {
        return [
            'message' => fake()->sentence(),
            'link_url' => fake()->optional(0.6)->url(),
            'link_label' => fake()->optional(0.6)->words(2, true),
            'bg_color' => fake()->hexColor(),
            'text_color' => '#FFFFFF',
            'is_active' => true,
            'starts_at' => null,
            'ends_at' => null,
        ];
    }

    public function withSchedule(): static
    {
        return $this->state([
            'starts_at' => now(),
            'ends_at' => now()->addDays(fake()->numberBetween(1, 30)),
        ]);
    }
}
