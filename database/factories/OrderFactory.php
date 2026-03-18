<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\OrderStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        $shippingCost = fake()->randomElement([0, 0, 50, 99]);
        $totalAmount = fake()->randomFloat(2, 100, 5000);

        return [
            'user_id' => User::factory(),
            'status_id' => OrderStatus::factory(),
            'total_amount' => $totalAmount,
            'payment_method' => fake()->randomElement(['card', 'transfer', 'cash', 'paypal']),
            'shipping_cost' => $shippingCost,
            'tracking_number' => 'MS-' . strtoupper(Str::random(10)),
        ];
    }
}
