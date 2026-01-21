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
     * Display a listing of orders for the authenticated user.
     */
    public function index()
    {
        $orders = $this->orderService->getUserOrders(auth()->id());

        return OrderResource::collection($orders);
    }

    /**
     * Store a newly created order.
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
     * Display the specified order.
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
