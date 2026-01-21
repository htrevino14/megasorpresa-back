<?php

namespace App\DTOs;

readonly class OrderItemDTO
{
    public function __construct(
        public int $product_id,
        public int $quantity,
        public float $unit_price,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            product_id: (int) $data['product_id'],
            quantity: (int) $data['quantity'],
            unit_price: (float) $data['unit_price'],
        );
    }
}
