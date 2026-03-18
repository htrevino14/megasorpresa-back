<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NewsletterCategory>
 */
class NewsletterCategoryFactory extends Factory
{
    public function definition(): array
    {
        $label = fake()->unique()->words(2, true);

        return [
            'label' => ucwords($label),
            'slug' => Str::slug($label),
            'sort_order' => fake()->numberBetween(0, 10),
            'is_active' => true,
        ];
    }
}
