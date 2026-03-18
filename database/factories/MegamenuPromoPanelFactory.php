<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\MegamenuCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MegamenuPromoPanel>
 */
class MegamenuPromoPanelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'megamenu_category_id' => MegamenuCategory::factory(),
            'badge' => fake()->optional(0.6)->randomElement(['Nuevo', 'Oferta', '¡Hot!', 'Exclusivo', 'Limitado']),
            'title' => fake()->sentence(3),
            'description' => fake()->optional(0.7)->sentence(),
            'emoji' => fake()->optional(0.5)->randomElement(['🎁', '🎉', '🧸', '🎮', '⭐', '🏆']),
            'bg_color' => fake()->optional(0.6)->hexColor(),
            'link_text' => fake()->optional(0.7)->randomElement(['Ver más', 'Explorar', 'Ver todo']),
            'link_url' => fake()->optional(0.7)->url(),
            'image_url' => fake()->optional(0.5)->imageUrl(300, 400, 'promo'),
        ];
    }
}
