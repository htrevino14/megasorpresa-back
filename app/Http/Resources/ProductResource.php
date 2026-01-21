<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'sku' => $this->sku,
            'base_price' => $this->base_price,
            'description' => $this->description,
            'stock_quantity' => $this->stock_quantity,
            'is_active' => $this->is_active,
            'primary_image' => $this->whenLoaded('primaryImage', function () {
                return $this->primaryImage->first()?->url;
            }),
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'available_cities' => $this->whenLoaded('availableCities', function () {
                return $this->availableCities->pluck('id');
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
