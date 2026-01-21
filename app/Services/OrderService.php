<?php

namespace App\Services;

use App\DTOs\OrderDTO;
use App\Models\Order;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\OrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        private CouponService $couponService
    ) {}

    /**
     * Create a new order with transaction support.
     */
    public function createOrder(OrderDTO $dto): Order
    {
        return DB::transaction(function () use ($dto) {
            // Calculate totals
            $subtotal = $this->calculateSubtotal($dto->items);
            $discount = 0;

            // Apply coupon if provided
            if ($dto->coupon_code) {
                $coupon = Coupon::where('code', $dto->coupon_code)->first();
                if ($coupon && $coupon->isValid()) {
                    $discount = $coupon->calculateDiscount($subtotal);
                }
            }

            // Calculate shipping cost
            $shippingCost = $this->calculateShippingCost($dto->delivery_slot_id);

            // Get pending status
            $pendingStatus = OrderStatus::firstOrCreate(['name' => 'pending']);

            // Create order
            $order = Order::create([
                'user_id' => $dto->user_id,
                'status_id' => $pendingStatus->id,
                'total_amount' => $subtotal - $discount + $shippingCost,
                'payment_method' => $dto->payment_method,
                'shipping_cost' => $shippingCost,
                'tracking_number' => $this->generateTrackingNumber(),
            ]);

            // Create order items
            foreach ($dto->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Validate stock
                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->base_price,
                ]);

                // Update product stock
                $product->decrement('stock_quantity', $item['quantity']);
            }

            // Create order details
            $order->detail()->create([
                'recipient_name' => $dto->recipient_name,
                'recipient_phone' => $dto->recipient_phone,
                'delivery_date' => $dto->delivery_date,
                'delivery_slot_id' => $dto->delivery_slot_id,
                'card_message' => $dto->card_message,
            ]);

            return $order->load(['items.product', 'detail', 'status']);
        });
    }

    /**
     * Calculate subtotal from items.
     */
    private function calculateSubtotal(array $items): float
    {
        $subtotal = 0;

        foreach ($items as $item) {
            $product = Product::findOrFail($item['product_id']);
            $subtotal += $product->base_price * $item['quantity'];
        }

        return $subtotal;
    }

    /**
     * Calculate shipping cost based on delivery slot.
     */
    private function calculateShippingCost(?int $deliverySlotId): float
    {
        if (!$deliverySlotId) {
            return 0;
        }

        $slot = \App\Models\DeliverySlot::find($deliverySlotId);
        return $slot ? $slot->additional_cost : 0;
    }

    /**
     * Generate a unique tracking number.
     */
    private function generateTrackingNumber(): string
    {
        do {
            $trackingNumber = 'MS-' . strtoupper(Str::random(10));
        } while (Order::where('tracking_number', $trackingNumber)->exists());

        return $trackingNumber;
    }

    /**
     * Get order by ID.
     */
    public function getOrder(int $orderId): Order
    {
        return Order::with(['items.product', 'detail.deliverySlot', 'status', 'user'])
            ->findOrFail($orderId);
    }

    /**
     * Get orders for a user.
     */
    public function getUserOrders(int $userId)
    {
        return Order::where('user_id', $userId)
            ->with(['items.product', 'detail', 'status'])
            ->latest()
            ->paginate(15);
    }
}
