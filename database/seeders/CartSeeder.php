<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\City;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CartSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $products = Product::where('is_active', true)->get();
        $cities = City::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            return;
        }

        // Create carts for some users
        $sampleCarts = [
            [
                'user_index' => 0,
                'session_id' => Str::random(40),
                'shipping_zip_code' => '01000',
                'shipping_city_id' => $cities->isNotEmpty() ? $cities->random()->id : null,
                'scheduled_delivery_date' => now()->addDays(5)->format('Y-m-d'),
                'items' => [
                    ['product_index' => 0, 'quantity' => 2],
                    ['product_index' => 1, 'quantity' => 1],
                ],
            ],
            [
                'user_index' => 1,
                'session_id' => Str::random(40),
                'shipping_zip_code' => '03100',
                'shipping_city_id' => $cities->isNotEmpty() ? $cities->random()->id : null,
                'scheduled_delivery_date' => now()->addDays(3)->format('Y-m-d'),
                'items' => [
                    ['product_index' => 2, 'quantity' => 1],
                    ['product_index' => 3, 'quantity' => 3],
                    ['product_index' => 4, 'quantity' => 1],
                ],
            ],
            [
                // Guest cart (no user)
                'user_index' => null,
                'session_id' => Str::random(40),
                'shipping_zip_code' => null,
                'shipping_city_id' => null,
                'scheduled_delivery_date' => null,
                'items' => [
                    ['product_index' => 5, 'quantity' => 1],
                ],
            ],
        ];

        foreach ($sampleCarts as $cartData) {
            $cart = Cart::create([
                'user_id' => $cartData['user_index'] !== null ? $users[$cartData['user_index']]->id : null,
                'session_id' => $cartData['session_id'],
                'shipping_zip_code' => $cartData['shipping_zip_code'],
                'shipping_city_id' => $cartData['shipping_city_id'],
                'scheduled_delivery_date' => $cartData['scheduled_delivery_date'],
            ]);

            foreach ($cartData['items'] as $itemData) {
                if (isset($products[$itemData['product_index']])) {
                    $product = $products[$itemData['product_index']];

                    CartItem::create([
                        'cart_id' => $cart->id,
                        'product_id' => $product->id,
                        'quantity' => $itemData['quantity'],
                        'price_at_addition' => $product->base_price,
                    ]);
                }
            }
        }
    }
}
