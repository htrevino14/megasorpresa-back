<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // Identificadores
            'id' => $this->id,
            'order_number' => $this->tracking_number ?? ('MS-' . str_pad((string) $this->id, 6, '0', STR_PAD_LEFT)),
            'tracking_number' => $this->tracking_number,

            // Estado
            'status' => $this->whenLoaded('status', fn () => [
                'id' => $this->status->id,
                'name' => $this->status->name,
            ]),
            'status_name' => $this->whenLoaded('status', fn () => $this->status->name),

            // Datos del destinatario / entrega (desde order_details)
            'recipient_name' => $this->whenLoaded('detail', fn () => $this->detail?->recipient_name),
            'date_legend' => $this->buildDateLegend(),

            // Imagen representativa (primer producto del pedido)
            'product_image_url' => $this->resolveProductImageUrl(),

            // Importes / pago
            'total_amount' => $this->total_amount,
            'shipping_cost' => $this->shipping_cost,
            'payment_method' => $this->payment_method,

            // Relaciones expandidas (solo si fueron cargadas)
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'detail' => new OrderDetailResource($this->whenLoaded('detail')),
            'user' => new UserResource($this->whenLoaded('user')),

            // Timestamps
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Construye la leyenda de fecha contextual al estado del pedido.
     *  - Entregado  → "Entregado el {fecha de entrega o actualización}"
     *  - Cancelado  → "Cancelado el {fecha de actualización}"
     *  - Resto      → "Para entrega el {fecha de entrega}" si existe,
     *                 si no "Creado el {fecha de creación}"
     */
    protected function buildDateLegend(): ?string
    {
        if (! $this->relationLoaded('status') || ! $this->status) {
            return null;
        }

        $statusName = mb_strtolower($this->status->name);
        $deliveryDate = $this->relationLoaded('detail') ? $this->detail?->delivery_date : null;

        if (str_contains($statusName, 'entregad')) {
            $date = $deliveryDate ?? $this->updated_at;

            return $date ? 'Entregado el ' . $date->translatedFormat('d \d\e F Y') : 'Entregado';
        }

        if (str_contains($statusName, 'cancel')) {
            return 'Cancelado el ' . $this->updated_at?->translatedFormat('d \d\e F Y');
        }

        if ($deliveryDate) {
            return 'Para entrega el ' . $deliveryDate->translatedFormat('d \d\e F Y');
        }

        return 'Creado el ' . $this->created_at?->translatedFormat('d \d\e F Y');
    }

    /**
     * Toma la imagen del primer producto del pedido (primaria si existe).
     */
    protected function resolveProductImageUrl(): ?string
    {
        if (! $this->relationLoaded('items')) {
            return null;
        }

        foreach ($this->items as $item) {
            $product = $item->product;
            if (! $product || ! $product->relationLoaded('images')) {
                continue;
            }

            $primary = $product->images->firstWhere('is_primary', true);
            $image = $primary ?? $product->images->first();

            if ($image) {
                return $image->url;
            }
        }

        return null;
    }
}
