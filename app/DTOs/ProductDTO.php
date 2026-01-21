<?php

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class ProductDTO
{
    public function __construct(
        public string $name,
        public string $slug,
        public string $sku,
        public float $base_price,
        public ?string $description,
        public int $stock_quantity,
        public bool $is_active,
        public ?array $category_ids = null,
        public ?array $availability_zones = null,
        public ?array $images = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            slug: $request->input('slug'),
            sku: $request->input('sku'),
            base_price: (float) $request->input('base_price'),
            description: $request->input('description'),
            stock_quantity: (int) $request->input('stock_quantity', 0),
            is_active: (bool) $request->input('is_active', true),
            category_ids: $request->input('category_ids'),
            availability_zones: $request->input('availability_zones'),
            images: $request->input('images'),
        );
    }
}
