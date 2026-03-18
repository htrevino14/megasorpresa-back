<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->words(fake()->numberBetween(2, 4), true);

        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name) . '-' . fake()->unique()->numerify('###'),
            'sku' => strtoupper(fake()->unique()->bothify('TOY-####-??')),
            'base_price' => fake()->randomFloat(2, 50, 2000),
            'description' => fake()->optional()->paragraph(),
            'stock_quantity' => fake()->numberBetween(0, 100),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function outOfStock(): static
    {
        return $this->state(['stock_quantity' => 0]);
    }
}
