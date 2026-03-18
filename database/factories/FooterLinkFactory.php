<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\FooterSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FooterLink>
 */
class FooterLinkFactory extends Factory
{
    public function definition(): array
    {
        return [
            'footer_section_id' => FooterSection::factory(),
            'label' => ucfirst(fake()->words(2, true)),
            'url' => fake()->url(),
            'icon' => fake()->optional(0.3)->randomElement(['icon-home', 'icon-info', 'icon-phone', 'icon-mail']),
            'open_in_new_tab' => fake()->boolean(20),
            'sort_order' => fake()->numberBetween(0, 20),
            'is_active' => true,
        ];
    }
}
