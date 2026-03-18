<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AgeGroup>
 */
class AgeGroupFactory extends Factory
{
    public function definition(): array
    {
        $label = fake()->unique()->numerify('# a ## años');

        return [
            'label' => $label,
            'sublabel' => fake()->words(3, true),
            'slug' => Str::slug($label),
            'bg_color' => fake()->hexColor(),
            'text_color' => '#FFFFFF',
            'category_id_destination' => null,
            'sort_order' => fake()->numberBetween(0, 20),
            'is_active' => true,
        ];
    }
}
