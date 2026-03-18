<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\DeliverySlot;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderDetail>
 */
class OrderDetailFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'recipient_name' => fake()->name(),
            'recipient_phone' => fake()->phoneNumber(),
            'delivery_date' => fake()->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
            'delivery_slot_id' => null,
            'card_message' => fake()->optional(0.7)->sentence(),
        ];
    }

    public function withDeliverySlot(): static
    {
        return $this->state(['delivery_slot_id' => DeliverySlot::factory()]);
    }
}
