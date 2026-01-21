<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'recipient_name' => $this->recipient_name,
            'recipient_phone' => $this->recipient_phone,
            'delivery_date' => $this->delivery_date->format('Y-m-d'),
            'delivery_slot' => $this->whenLoaded('deliverySlot', function () {
                return [
                    'id' => $this->deliverySlot->id,
                    'start_time' => $this->deliverySlot->start_time,
                    'end_time' => $this->deliverySlot->end_time,
                    'additional_cost' => $this->deliverySlot->additional_cost,
                ];
            }),
            'card_message' => $this->card_message,
        ];
    }
}
