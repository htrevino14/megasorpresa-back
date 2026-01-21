<?php

namespace App\DTOs;

readonly class CouponDTO
{
    public function __construct(
        public string $code,
        public string $discount_type,
        public float $value,
        public ?float $min_purchase,
        public ?string $expiry_date,
    ) {}
}
