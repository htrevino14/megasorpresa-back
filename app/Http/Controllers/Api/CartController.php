<?php

namespace App\Http\Controllers\Api;

use App\DTOs\CartDTO;
use App\DTOs\CartItemDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartDetailsRequest;
use App\Http\Requests\UpdateCartQuantityRequest;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/cart",
     *     summary="Obtener el carrito actual",
     *     description="Retorna el carrito del usuario autenticado o de la sesión actual (invitado).",
     *     tags={"Carrito"},
     *     @OA\Response(
     *         response=200,
     *         description="Carrito obtenido exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/Cart")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $sessionId = $request->session()->getId();
        $userId = $request->user()?->id;

        $cart = $this->cartService->getOrCreateCart($sessionId, $userId);

        return new CartResource($cart);
    }

    /**
     * @OA\Post(
     *     path="/api/cart/add",
     *     summary="Agregar producto al carrito",
     *     description="Agrega un producto al carrito. Si el producto ya existe, incrementa la cantidad.",
     *     tags={"Carrito"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id","quantity"},
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="quantity", type="integer", example=2, minimum=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Producto agregado exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Product added to cart successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Cart")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación o stock insuficiente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Failed to add product to cart"),
     *             @OA\Property(property="error", type="string", example="Insufficient stock for product")
     *         )
     *     )
     * )
     */
    public function add(AddToCartRequest $request)
    {
        try {
            $sessionId = $request->session()->getId();
            $userId = $request->user()?->id;

            $cart = $this->cartService->getOrCreateCart($sessionId, $userId);
            $cartItemDTO = CartItemDTO::fromRequest($request);

            $this->cartService->addItem($cart, $cartItemDTO);

            $cart = $cart->fresh(['items.product', 'shippingCity']);

            return response()->json([
                'message' => 'Product added to cart successfully',
                'data' => new CartResource($cart),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to add product to cart',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/cart/update-quantity",
     *     summary="Actualizar cantidad de un item del carrito",
     *     description="Modifica la cantidad de un producto en el carrito.",
     *     tags={"Carrito"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id","quantity"},
     *             @OA\Property(property="product_id", type="integer", example=1),
     *             @OA\Property(property="quantity", type="integer", example=3, minimum=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cantidad actualizada exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Cart item quantity updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Cart")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Producto no encontrado en el carrito",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Product not found in cart")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación o stock insuficiente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Failed to update cart item quantity"),
     *             @OA\Property(property="error", type="string", example="Insufficient stock")
     *         )
     *     )
     * )
     */
    public function updateQuantity(UpdateCartQuantityRequest $request)
    {
        try {
            $sessionId = $request->session()->getId();
            $userId = $request->user()?->id;

            $cart = $this->cartService->getOrCreateCart($sessionId, $userId);

            $this->cartService->updateItemQuantity(
                $cart,
                $request->input('product_id'),
                $request->input('quantity')
            );

            $cart = $cart->fresh(['items.product', 'shippingCity']);

            return response()->json([
                'message' => 'Cart item quantity updated successfully',
                'data' => new CartResource($cart),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update cart item quantity',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/cart/remove/{productId}",
     *     summary="Eliminar producto del carrito",
     *     description="Remueve un producto del carrito completamente.",
     *     tags={"Carrito"},
     *     @OA\Parameter(
     *         name="productId",
     *         in="path",
     *         description="ID del producto a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Producto eliminado exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Product removed from cart successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Cart")
     *         )
     *     )
     * )
     */
    public function remove(Request $request, int $productId)
    {
        $sessionId = $request->session()->getId();
        $userId = $request->user()?->id;

        $cart = $this->cartService->getOrCreateCart($sessionId, $userId);

        $this->cartService->removeItem($cart, $productId);

        $cart = $cart->fresh(['items.product', 'shippingCity']);

        return response()->json([
            'message' => 'Product removed from cart successfully',
            'data' => new CartResource($cart),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/cart/details",
     *     summary="Actualizar información de envío del carrito",
     *     description="Actualiza el código postal, ciudad y fecha programada de entrega del carrito.",
     *     tags={"Carrito"},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="shipping_zip_code", type="string", example="12345", maxLength=10),
     *             @OA\Property(property="shipping_city_id", type="integer", example=1),
     *             @OA\Property(property="scheduled_delivery_date", type="string", format="date", example="2026-05-15", description="Fecha programada de entrega (debe ser posterior a hoy)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles actualizados exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Cart details updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Cart")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Failed to update cart details"),
     *             @OA\Property(property="error", type="string", example="Delivery date must be in the future")
     *         )
     *     )
     * )
     */
    public function updateDetails(UpdateCartDetailsRequest $request)
    {
        try {
            $sessionId = $request->session()->getId();
            $userId = $request->user()?->id;

            $cart = $this->cartService->getOrCreateCart($sessionId, $userId);
            $cartDTO = CartDTO::fromRequest($request);

            $cart = $this->cartService->updateDeliveryDetails($cart, $cartDTO);

            return response()->json([
                'message' => 'Cart details updated successfully',
                'data' => new CartResource($cart),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update cart details',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/cart/clear",
     *     summary="Vaciar el carrito",
     *     description="Elimina todos los items del carrito.",
     *     tags={"Carrito"},
     *     @OA\Response(
     *         response=200,
     *         description="Carrito vaciado exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Cart cleared successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Cart")
     *         )
     *     )
     * )
     */
    public function clear(Request $request)
    {
        $sessionId = $request->session()->getId();
        $userId = $request->user()?->id;

        $cart = $this->cartService->getOrCreateCart($sessionId, $userId);

        $this->cartService->clearCart($cart);

        $cart = $cart->fresh(['items.product', 'shippingCity']);

        return response()->json([
            'message' => 'Cart cleared successfully',
            'data' => new CartResource($cart),
        ]);
    }
}
