<?php

namespace App\Services;

use App\DTOs\CheckoutDTO;
use App\DTOs\OrderDTO;
use App\Models\Cart;
use App\Models\DeliverySlot;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\UserAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        private CouponService $couponService,
        private CartService $cartService,
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

    /**
     * Procesa el checkout completo del wizard (5 pasos) de forma atómica.
     *
     * Guarda en una sola transacción:
     *  - La dirección de envío en `user_addresses`.
     *  - El pedido principal en `orders` (con cálculo de totales y stock).
     *  - Los items en `order_items` y descuento de stock.
     *  - El detalle del envío (teléfono, destinatario, dedicatoria) en `order_details`.
     *  - Limpia el carrito del usuario al confirmar.
     *
     * @throws \RuntimeException Si el carrito está vacío o no hay stock.
     */
    public function processCheckout(CheckoutDTO $dto, ?string $cartToken = null): Order
    {
        return DB::transaction(function () use ($dto, $cartToken) {
            // 1. Recuperar el carrito del usuario.
            $cart = $this->cartService->getOrCreateCart($cartToken, $dto->user_id);

            if ($cart->items->isEmpty()) {
                throw new \RuntimeException('El carrito está vacío.');
            }

            // 2. Resolver entidades referenciadas y verificar stock.
            $deliverySlot = DeliverySlot::findOrFail($dto->delivery_slot_id);
            $paymentMethod = PaymentMethod::findOrFail($dto->payment_method_id);

            $subtotal = 0.0;
            foreach ($cart->items as $item) {
                /** @var Product $product */
                $product = $item->product;

                if (!$product || !$product->is_active) {
                    throw new \RuntimeException("El producto «{$item->product_id}» ya no está disponible.");
                }

                if ($product->stock_quantity < $item->quantity) {
                    throw new \RuntimeException(
                        "Stock insuficiente para «{$product->name}». Disponible: {$product->stock_quantity}."
                    );
                }

                $subtotal += (float) $item->price_at_addition * (int) $item->quantity;
            }

            $shippingCost = (float) ($deliverySlot->additional_cost ?? 0);
            $totalAmount = $subtotal + $shippingCost;

            // 3. Guardar la dirección de envío en user_addresses.
            $address = UserAddress::create([
                'user_id' => $dto->user_id,
                'street' => $dto->recipient['street'],
                'ext_number' => trim(
                    $dto->recipient['ext_number']
                    . ($dto->recipient['interior_number'] ? ' Int. ' . $dto->recipient['interior_number'] : '')
                ),
                'neighborhood' => $dto->recipient['neighborhood'],
                'city_id' => $dto->recipient['city_id'],
                'zip_code' => $dto->recipient['zip_code'],
                'references' => $dto->recipient['references'],
            ]);

            // 4. Crear el pedido principal.
            $pendingStatus = OrderStatus::firstOrCreate(['name' => 'pending']);

            $order = Order::create([
                'user_id' => $dto->user_id,
                'status_id' => $pendingStatus->id,
                'total_amount' => $totalAmount,
                'payment_method' => $paymentMethod->name,
                'shipping_cost' => $shippingCost,
                'tracking_number' => $this->generateTrackingNumber(),
            ]);

            // 5. Crear los items del pedido y descontar stock.
            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->price_at_addition,
                ]);

                $item->product->decrement('stock_quantity', $item->quantity);
            }

            // 6. Guardar teléfono, destinatario y dedicatoria en order_details.
            $order->detail()->create([
                'recipient_name' => $dto->recipient['recipient_name'],
                'recipient_phone' => $dto->recipient['phone'] ?? $dto->buyer_phone,
                'delivery_date' => $dto->delivery_date,
                'delivery_slot_id' => $dto->delivery_slot_id,
                'card_message' => $dto->composedCardMessage(),
            ]);

            // 7. Vaciar el carrito al confirmar.
            $this->cartService->clearCart($cart);

            return $order->load([
                'items.product',
                'detail.deliverySlot',
                'status',
            ])->setRelation('shippingAddress', $address);
        });
    }
}
