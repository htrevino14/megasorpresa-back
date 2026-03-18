<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FooterSection>
 */
class FooterSectionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => ucwords(fake()->unique()->words(2, true)),
            'sort_order' => fake()->numberBetween(0, 20),
            'is_active' => true,
        ];
    }
}
