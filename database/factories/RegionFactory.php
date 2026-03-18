<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Region>
 */
class RegionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->country(),
            'code' => strtoupper(fake()->unique()->lexify('??')),
            'flag_emoji' => fake()->optional()->randomElement(['🇲🇽', '🇺🇸', '🇨🇦', '🇪🇸', '🇧🇷']),
            'flag_url' => fake()->optional()->imageUrl(32, 24, 'flags'),
            'locale' => fake()->optional()->locale(),
            'currency_code' => fake()->optional()->currencyCode(),
            'sort_order' => fake()->numberBetween(0, 100),
            'is_active' => true,
            'is_default' => false,
        ];
    }

    public function default(): static
    {
        return $this->state(['is_default' => true, 'is_active' => true]);
    }
}
