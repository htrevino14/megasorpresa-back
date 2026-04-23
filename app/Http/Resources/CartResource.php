<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cart_token' => $this->session_id,
            'session_id' => $this->session_id,
            'shipping_zip_code' => $this->shipping_zip_code,
            'shipping_city' => $this->when(
                $this->relationLoaded('shippingCity') && $this->shippingCity,
                function () {
                    return [
                        'id' => $this->shippingCity->id,
                        'name' => $this->shippingCity->name,
                    ];
                }
            ),
            'scheduled_delivery_date' => $this->scheduled_delivery_date?->format('Y-m-d'),
            'items' => CartItemResource::collection($this->whenLoaded('items')),
            'subtotal' => $this->subtotal,
            'total_items' => $this->total_items,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
