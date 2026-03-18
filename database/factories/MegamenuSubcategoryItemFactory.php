<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\MegamenuSubcategoryGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MegamenuSubcategoryItem>
 */
class MegamenuSubcategoryItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'megamenu_subcategory_group_id' => MegamenuSubcategoryGroup::factory(),
            'label' => ucwords(fake()->words(2, true)),
            'category_id_destination' => null,
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }
}
