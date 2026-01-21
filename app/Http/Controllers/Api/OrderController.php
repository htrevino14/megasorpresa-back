<?php

namespace App\Http\Controllers\Api;

use App\DTOs\OrderDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/orders",
     *     summary="Listar órdenes del usuario autenticado",
     *     description="Obtiene un listado de todas las órdenes realizadas por el usuario autenticado.",
     *     tags={"Órdenes"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Listado de órdenes obtenido exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Order")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")
     *     )
     * )
     */
    public function index()
    {
        $orders = $this->orderService->getUserOrders(auth()->id());

        return OrderResource::collection($orders);
    }

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Crear una nueva orden (Checkout)",
     *     description="Procesa una nueva orden de compra. Valida stock, aplica cupones si corresponde, y crea la orden con todos sus items.",
     *     tags={"Órdenes"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"items","recipient_name","recipient_phone","delivery_date"},
     *             @OA\Property(
     *                 property="items",
     *                 type="array",
     *                 description="Lista de productos a ordenar",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"product_id","quantity"},
     *                     @OA\Property(property="product_id", type="integer", example=1),
     *                     @OA\Property(property="quantity", type="integer", example=2, minimum=1)
     *                 )
     *             ),
     *             @OA\Property(property="coupon_code", type="string", example="PROMO2026", description="Código de cupón de descuento (opcional)"),
     *             @OA\Property(property="recipient_name", type="string", example="María García", maxLength=255),
     *             @OA\Property(property="recipient_phone", type="string", example="+1234567890", maxLength=20),
     *             @OA\Property(property="delivery_date", type="string", format="date", example="2026-02-15", description="Fecha de entrega (debe ser posterior a hoy)"),
     *             @OA\Property(property="delivery_slot_id", type="integer", example=1, description="ID del horario de entrega (opcional)"),
     *             @OA\Property(property="card_message", type="string", example="¡Feliz cumpleaños!", maxLength=500, description="Mensaje para la tarjeta (opcional)"),
     *             @OA\Property(property="payment_method", type="string", example="card", enum={"cash","card","transfer"}, description="Método de pago (opcional)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Orden creada exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/Order")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos de validación inválidos o fallo al crear la orden",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(ref="#/components/schemas/ValidationError"),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="message", type="string", example="Failed to create order"),
     *                     @OA\Property(property="error", type="string", example="Insufficient stock for product")
     *                 )
     *             }
     *         )
     *     )
     * )
     */
    public function store(StoreOrderRequest $request)
    {
        try {
            $orderDTO = OrderDTO::fromRequest($request);
            $order = $this->orderService->createOrder($orderDTO);

            return (new OrderResource($order))
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create order',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/orders/{id}",
     *     summary="Obtener detalle de una orden específica",
     *     description="Obtiene la información completa de una orden específica. El usuario solo puede ver sus propias órdenes.",
     *     tags={"Órdenes"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la orden",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalle de la orden obtenido exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", ref="#/components/schemas/Order")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No autorizado para ver esta orden",
     *         @OA\JsonContent(ref="#/components/schemas/ForbiddenError")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Orden no encontrada",
     *         @OA\JsonContent(ref="#/components/schemas/NotFoundError")
     *     )
     * )
     */
    public function show(int $id)
    {
        $order = $this->orderService->getOrder($id);

        // Ensure user can only view their own orders
        if ($order->user_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        return new OrderResource($order);
    }
}
