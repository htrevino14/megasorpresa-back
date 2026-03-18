<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductImage>
 */
class ProductImageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'url' => fake()->imageUrl(800, 600, 'toys'),
            'is_primary' => false,
            'order' => fake()->numberBetween(0, 10),
        ];
    }

    public function primary(): static
    {
        return $this->state(['is_primary' => true, 'order' => 0]);
    }
}
