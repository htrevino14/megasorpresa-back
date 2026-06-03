<?php

namespace App\Services;

use App\DTOs\OrderDTO;
use App\Http\Requests\StoreCheckoutRequest;
use App\Models\Order;
use App\Models\OrderStatus;
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
     * Lista paginada de pedidos de un usuario, ordenada por fecha descendente.
     *
     * Carga las relaciones necesarias para evitar consultas N+1 en el listado
     * de "Mis pedidos":
     *  - `status`           para el nombre del estado.
     *  - `detail`           para el destinatario y la fecha de entrega.
     *  - `items.product.images` para mostrar la imagen del primer producto.
     */
    public function getUserOrders(int $userId, int $perPage = 10)
    {
        return Order::where('user_id', $userId)
            ->with([
                'status',
                'detail',
                'items.product.images',
            ])
            ->latest()
            ->paginate($perPage);
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
    /**
     * Procesa el checkout completo y persiste el pedido de forma atómica.
     *
     * Toda la operación se ejecuta dentro de `DB::transaction()` para garantizar
     * la integridad: si cualquier paso falla, no se guarda nada.
     *
     * Flujo:
     *  1. Encuentra o crea la dirección de envío en `user_addresses`.
     *  2. Crea el pedido en `orders` (estado inicial "Pendiente", subtotal,
     *     total y método de pago del request).
     *  3. Crea los items en `order_items` desde el carrito y descuenta stock.
     *  4. Crea el detalle en `order_details` (destinatario, teléfono, fecha,
     *     bloque de horario, dedicatoria y firma).
     *  5. Vacía el carrito al confirmar.
     *
     * @throws \RuntimeException Si el carrito está vacío o no hay stock suficiente.
     */
    public function processCheckout(StoreCheckoutRequest $request, int $userId): Order
    {
        $data = $request->validated();

        return DB::transaction(function () use ($request, $data, $userId) {
            // 1. Encontrar o crear la dirección de envío del usuario.
            $address = UserAddress::firstOrCreate(
                [
                    'user_id' => $userId,
                    'street' => $data['street'],
                    'ext_number' => $data['ext_number'],
                    'neighborhood' => $data['neighborhood'],
                    'zip_code' => $data['zip_code'],
                    'city_id' => $data['city_id'],
                    'state_id' => $data['state_id'],
                ],
                [
                    'dwelling_type' => $data['dwelling_type'],
                    'references' => $data['references'] ?? null,
                ]
            );

            // 2. Recuperar el carrito y validar stock antes de crear el pedido.
            $cart = $this->cartService->getOrCreateCart(
                $request->header('X-Cart-Token'),
                $userId,
            );

            if ($cart->items->isEmpty()) {
                throw new \RuntimeException('El carrito está vacío.');
            }

            foreach ($cart->items as $item) {
                /** @var Product $product */
                $product = $item->product;

                if (! $product || ! $product->is_active) {
                    throw new \RuntimeException("El producto «{$item->product_id}» ya no está disponible.");
                }

                if ($product->stock_quantity < $item->quantity) {
                    throw new \RuntimeException(
                        "Stock insuficiente para «{$product->name}». Disponible: {$product->stock_quantity}."
                    );
                }
            }

            // 3. Crear el pedido principal con estado inicial "Pendiente".
            $pendingStatus = OrderStatus::firstOrCreate(['name' => 'Pendiente']);

            $subtotal = (float) $data['subtotal'];
            $total = (float) $data['total'];
            $shippingCost = max(0.0, $total - $subtotal);

            $order = Order::create([
                'user_id' => $userId,
                'status_id' => $pendingStatus->id,
                'total_amount' => $total,
                'payment_method' => $data['payment_method'],
                'shipping_cost' => $shippingCost,
                'tracking_number' => $this->generateTrackingNumber(),
            ]);

            // 4. Crear los items del pedido y descontar stock.
            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->price_at_addition,
                ]);

                $item->product->decrement('stock_quantity', $item->quantity);
            }

            // 5. Crear el detalle del envío en order_details.
            $order->detail()->create([
                'recipient_name' => $request->input('recipient_name') ?? $order->user?->name ?? 'Destinatario',
                'recipient_phone' => $data['recipient_phone'],
                'delivery_date' => $data['delivery_date'],
                'delivery_slot_id' => $data['delivery_slot_id'],
                'card_message' => $data['card_message'] ?? null,
                'signature' => $data['signature'] ?? null,
            ]);

            // 6. Vaciar el carrito al confirmar.
            $this->cartService->clearCart($cart);

            return $order->load([
                'items.product',
                'detail.deliverySlot',
                'status',
            ])->setRelation('shippingAddress', $address);
        });
    }
}
