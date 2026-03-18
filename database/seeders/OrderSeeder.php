<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\DeliverySlot;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $products = Product::where('is_active', true)->get();
        $pendingStatus = OrderStatus::where('name', 'Pendiente')->first();
        $processingStatus = OrderStatus::where('name', 'Procesando')->first();
        $deliveredStatus = OrderStatus::where('name', 'Entregado')->first();

        if ($users->isEmpty() || $products->isEmpty() || ! $pendingStatus) {
            return;
        }

        $sampleOrders = [
            [
                'tracking_number' => 'MS-SEED001',
                'status' => $pendingStatus,
                'payment_method' => 'card',
                'shipping_cost' => 99.00,
                'items' => [
                    ['product_index' => 0, 'quantity' => 1],
                    ['product_index' => 1, 'quantity' => 2],
                ],
                'recipient_name' => 'Laura Sánchez',
                'recipient_phone' => '5512348765',
                'delivery_date' => now()->addDays(3)->format('Y-m-d'),
                'card_message' => '¡Feliz cumpleaños! Con mucho cariño.',
            ],
            [
                'tracking_number' => 'MS-SEED002',
                'status' => $processingStatus ?? $pendingStatus,
                'payment_method' => 'transfer',
                'shipping_cost' => 0.00,
                'items' => [
                    ['product_index' => 2, 'quantity' => 1],
                ],
                'recipient_name' => 'Roberto Jiménez',
                'recipient_phone' => '5587654321',
                'delivery_date' => now()->addDays(5)->format('Y-m-d'),
                'card_message' => null,
            ],
            [
                'tracking_number' => 'MS-SEED003',
                'status' => $deliveredStatus ?? $pendingStatus,
                'payment_method' => 'paypal',
                'shipping_cost' => 50.00,
                'items' => [
                    ['product_index' => 3, 'quantity' => 1],
                    ['product_index' => 4, 'quantity' => 1],
                    ['product_index' => 5, 'quantity' => 2],
                ],
                'recipient_name' => 'Sofía Ramírez',
                'recipient_phone' => '5523456789',
                'delivery_date' => now()->subDays(2)->format('Y-m-d'),
                'card_message' => 'Para el día del niño con todo nuestro amor.',
            ],
        ];

        $deliverySlot = DeliverySlot::first();
        $user = $users->first();

        foreach ($sampleOrders as $orderData) {
            if (Order::where('tracking_number', $orderData['tracking_number'])->exists()) {
                continue;
            }

            $items = $orderData['items'];
            $totalAmount = 0;

            foreach ($items as $item) {
                $product = $products->get($item['product_index']) ?? $products->first();
                $totalAmount += $product->base_price * $item['quantity'];
            }

            $totalAmount += $orderData['shipping_cost'];

            $order = Order::create([
                'user_id' => $user->id,
                'status_id' => $orderData['status']->id,
                'total_amount' => $totalAmount,
                'payment_method' => $orderData['payment_method'],
                'shipping_cost' => $orderData['shipping_cost'],
                'tracking_number' => $orderData['tracking_number'],
            ]);

            foreach ($items as $item) {
                $product = $products->get($item['product_index']) ?? $products->first();
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->base_price,
                ]);
            }

            OrderDetail::create([
                'order_id' => $order->id,
                'recipient_name' => $orderData['recipient_name'],
                'recipient_phone' => $orderData['recipient_phone'],
                'delivery_date' => $orderData['delivery_date'],
                'delivery_slot_id' => $deliverySlot?->id,
                'card_message' => $orderData['card_message'],
            ]);
        }
    }
}
