<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'user_id' => User::factory(),
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->optional(0.8)->paragraph(),
            'is_approved' => fake()->boolean(70),
        ];
    }

    public function approved(): static
    {
        return $this->state(['is_approved' => true]);
    }

    public function pending(): static
    {
        return $this->state(['is_approved' => false]);
    }
}
