<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HeroSlide>
 */
class HeroSlideFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'subtitle' => fake()->optional(0.7)->sentence(),
            'cta_text' => fake()->optional(0.8)->randomElement(['Ver más', 'Comprar ahora', 'Descubrir', 'Ver colección']),
            'cta_link' => fake()->optional(0.8)->url(),
            'image_url_desktop' => fake()->imageUrl(1920, 600, 'hero'),
            'image_url_mobile' => fake()->optional(0.6)->imageUrl(768, 500, 'hero'),
            'alt_text' => fake()->optional()->sentence(4),
            'bg_color' => fake()->optional(0.5)->hexColor(),
            'sort_order' => fake()->numberBetween(0, 10),
            'is_active' => true,
            'starts_at' => null,
            'ends_at' => null,
        ];
    }
}
