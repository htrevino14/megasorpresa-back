<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PaymentMethod>
 */
class PaymentMethodFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Tarjeta de crédito',
                'Tarjeta de débito',
                'PayPal',
                'Transferencia bancaria',
                'OXXO Pay',
                'Mercado Pago',
                'Apple Pay',
                'Google Pay',
            ]),
            'logo_url' => fake()->optional(0.7)->imageUrl(120, 60, 'payment'),
            'icon_class' => fake()->optional(0.5)->randomElement(['icon-visa', 'icon-mc', 'icon-paypal', 'icon-amex']),
            'sort_order' => fake()->numberBetween(0, 10),
            'is_active' => true,
        ];
    }
}
