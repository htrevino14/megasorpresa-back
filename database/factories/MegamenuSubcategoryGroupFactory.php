<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\MegamenuCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MegamenuSubcategoryGroup>
 */
class MegamenuSubcategoryGroupFactory extends Factory
{
    public function definition(): array
    {
        return [
            'megamenu_category_id' => MegamenuCategory::factory(),
            'title' => ucwords(fake()->words(2, true)),
            'category_id_destination' => null,
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
