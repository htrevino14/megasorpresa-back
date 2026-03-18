<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CategoryCarouselItem>
 */
class CategoryCarouselItemFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->words(2, true);

        return [
            'category_id' => null,
            'name' => ucwords($name),
            'slug' => Str::slug($name),
            'image_url' => fake()->imageUrl(200, 200, 'category'),
            'bg_color' => fake()->optional(0.7)->hexColor(),
            'sort_order' => fake()->numberBetween(0, 20),
            'is_active' => true,
        ];
    }
}
