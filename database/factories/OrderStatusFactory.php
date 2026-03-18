<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderStatus>
 */
class OrderStatusFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Pendiente',
                'Procesando',
                'Enviado',
                'Entregado',
                'Cancelado',
                'Reembolsado',
            ]),
        ];
    }
}
