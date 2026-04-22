<?php

namespace App\Services;

use App\DTOs\CartDTO;
use App\DTOs\CartItemDTO;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartService
{
    /**
     * Get or create a cart for the current session/user.
     */
    public function getOrCreateCart(string $sessionId, ?int $userId = null): Cart
    {
        // If user is logged in, try to find their cart first
        if ($userId) {
            $cart = Cart::byUser($userId)->first();

            // If found, update session_id to current session
            if ($cart) {
                $cart->update(['session_id' => $sessionId]);
                return $cart->load(['items.product', 'shippingCity']);
            }
        }

        // Otherwise, find cart by session
        $cart = Cart::bySession($sessionId)->first();

        // If no cart exists, create one
        if (!$cart) {
            $cart = Cart::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
            ]);
        }

        return $cart->load(['items.product', 'shippingCity']);
    }

    /**
     * Add a product to the cart.
     */
    public function addItem(Cart $cart, CartItemDTO $dto): CartItem
    {
        return DB::transaction(function () use ($cart, $dto) {
            $product = Product::findOrFail($dto->product_id);

            // Check if product is active
            if (!$product->is_active) {
                throw new \Exception("Product is not available: {$product->name}");
            }

            // Check stock availability
            $existingItem = $cart->items()->where('product_id', $dto->product_id)->first();
            $requestedQuantity = $dto->quantity;

            if ($existingItem) {
                $requestedQuantity += $existingItem->quantity;
            }

            if ($product->stock_quantity < $requestedQuantity) {
                throw new \Exception("Insufficient stock for product: {$product->name}. Available: {$product->stock_quantity}");
            }

            // If item already exists, increment quantity
            if ($existingItem) {
                $existingItem->increment('quantity', $dto->quantity);
                return $existingItem->fresh();
            }

            // Otherwise, create new cart item
            return $cart->items()->create([
                'product_id' => $dto->product_id,
                'quantity' => $dto->quantity,
                'price_at_addition' => $product->base_price,
            ]);
        });
    }

    /**
     * Update quantity of a cart item.
     */
    public function updateItemQuantity(Cart $cart, int $productId, int $quantity): CartItem
    {
        return DB::transaction(function () use ($cart, $productId, $quantity) {
            $cartItem = $cart->items()->where('product_id', $productId)->firstOrFail();
            $product = $cartItem->product;

            // Validate quantity is positive
            if ($quantity <= 0) {
                throw new \Exception('Quantity must be greater than zero');
            }

            // Check stock availability
            if ($product->stock_quantity < $quantity) {
                throw new \Exception("Insufficient stock for product: {$product->name}. Available: {$product->stock_quantity}");
            }

            $cartItem->update(['quantity' => $quantity]);

            return $cartItem->fresh();
        });
    }

    /**
     * Remove a product from the cart.
     */
    public function removeItem(Cart $cart, int $productId): void
    {
        $cart->items()->where('product_id', $productId)->delete();
    }

    /**
     * Update cart delivery details.
     */
    public function updateDeliveryDetails(Cart $cart, CartDTO $dto): Cart
    {
        // Validate delivery date is in the future
        if ($dto->scheduled_delivery_date &&
            strtotime($dto->scheduled_delivery_date) < strtotime('today')) {
            throw new \Exception('Delivery date must be in the future');
        }

        $cart->update([
            'shipping_zip_code' => $dto->shipping_zip_code,
            'shipping_city_id' => $dto->shipping_city_id,
            'scheduled_delivery_date' => $dto->scheduled_delivery_date,
        ]);

        return $cart->fresh(['items.product', 'shippingCity']);
    }

    /**
     * Clear all items from the cart.
     */
    public function clearCart(Cart $cart): void
    {
        $cart->items()->delete();
    }

    /**
     * Merge guest cart with user cart on login.
     */
    public function mergeGuestCart(string $guestSessionId, int $userId): Cart
    {
        return DB::transaction(function () use ($guestSessionId, $userId) {
            // Get guest cart
            $guestCart = Cart::bySession($guestSessionId)->first();

            // Get or create user cart
            $userCart = Cart::byUser($userId)->first();

            if (!$userCart) {
                // If user doesn't have a cart, just assign guest cart to user
                if ($guestCart) {
                    $guestCart->update(['user_id' => $userId]);
                    return $guestCart->load(['items.product', 'shippingCity']);
                }

                // Create new cart for user
                return Cart::create([
                    'user_id' => $userId,
                    'session_id' => $guestSessionId,
                ]);
            }

            // If guest cart exists, merge items into user cart
            if ($guestCart) {
                foreach ($guestCart->items as $guestItem) {
                    $existingItem = $userCart->items()
                        ->where('product_id', $guestItem->product_id)
                        ->first();

                    if ($existingItem) {
                        // Merge quantities (respecting stock limits)
                        $newQuantity = min(
                            $existingItem->quantity + $guestItem->quantity,
                            $guestItem->product->stock_quantity
                        );
                        $existingItem->update(['quantity' => $newQuantity]);
                    } else {
                        // Move item to user cart
                        $guestItem->update(['cart_id' => $userCart->id]);
                    }
                }

                // Delete guest cart
                $guestCart->delete();
            }

            // Update session
            $userCart->update(['session_id' => $guestSessionId]);

            return $userCart->load(['items.product', 'shippingCity']);
        });
    }

    /**
     * Get cart by ID.
     */
    public function getCart(int $cartId): Cart
    {
        return Cart::with(['items.product', 'shippingCity', 'user'])
            ->findOrFail($cartId);
    }
}
