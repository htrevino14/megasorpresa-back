<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Banner>
 */
class BannerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'image_url' => fake()->imageUrl(1200, 400, 'banners'),
            'link_to' => fake()->optional(0.7)->url(),
            'location' => fake()->optional()->randomElement(['home', 'catalog', 'checkout', 'sidebar']),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
