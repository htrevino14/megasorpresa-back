<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SocialLink>
 */
class SocialLinkFactory extends Factory
{
    public function definition(): array
    {
        $platform = fake()->unique()->randomElement([
            'Facebook', 'Instagram', 'Twitter', 'TikTok', 'YouTube', 'Pinterest', 'WhatsApp',
        ]);

        return [
            'platform' => $platform,
            'url' => 'https://' . strtolower($platform) . '.com/megasorpresa',
            'icon_class' => 'icon-' . strtolower($platform),
            'icon_svg' => null,
            'initial' => strtoupper(substr($platform, 0, 1)),
            'sort_order' => fake()->numberBetween(0, 10),
            'is_active' => true,
        ];
    }
}
