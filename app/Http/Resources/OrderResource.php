<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tracking_number' => $this->tracking_number,
            'status' => [
                'id' => $this->status->id,
                'name' => $this->status->name,
            ],
            'total_amount' => $this->total_amount,
            'shipping_cost' => $this->shipping_cost,
            'payment_method' => $this->payment_method,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'detail' => new OrderDetailResource($this->whenLoaded('detail')),
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
