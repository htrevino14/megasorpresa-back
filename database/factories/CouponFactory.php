<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Coupon>
 */
class CouponFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('??##??')),
            'discount_type' => fake()->randomElement(['percentage', 'fixed']),
            'value' => fake()->randomFloat(2, 5, 50),
            'min_purchase' => fake()->optional(0.6)->randomFloat(2, 100, 500),
            'expiry_date' => fake()->optional(0.7)->dateTimeBetween('now', '+6 months')->format('Y-m-d'),
        ];
    }

    public function percentage(): static
    {
        return $this->state([
            'discount_type' => 'percentage',
            'value' => fake()->numberBetween(5, 50),
        ]);
    }

    public function fixed(): static
    {
        return $this->state([
            'discount_type' => 'fixed',
            'value' => fake()->randomElement([20, 50, 100, 150, 200]),
        ]);
    }

    public function expired(): static
    {
        return $this->state([
            'expiry_date' => fake()->dateTimeBetween('-6 months', '-1 day')->format('Y-m-d'),
        ]);
    }
}
