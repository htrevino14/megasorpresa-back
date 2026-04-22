<?php

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class CartItemDTO
{
    public function __construct(
        public int $product_id,
        public int $quantity = 1,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            product_id: $request->input('product_id'),
            quantity: $request->input('quantity', 1),
        );
    }
}
