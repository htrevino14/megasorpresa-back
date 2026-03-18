<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductAddon>
 */
class ProductAddonFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Envoltorio de regalo',
                'Tarjeta personalizada',
                'Moño decorativo',
                'Caja premium',
                'Bolsa de regalo',
                'Papel de china',
                'Listón personalizado',
                'Globo de cumpleaños',
            ]),
            'price' => fake()->randomFloat(2, 10, 150),
        ];
    }
}
