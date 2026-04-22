<?php

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class CartDTO
{
    public function __construct(
        public ?int $user_id,
        public string $session_id,
        public ?string $shipping_zip_code = null,
        public ?int $shipping_city_id = null,
        public ?string $scheduled_delivery_date = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            user_id: $request->user()?->id,
            session_id: $request->session()->getId(),
            shipping_zip_code: $request->input('shipping_zip_code'),
            shipping_city_id: $request->input('shipping_city_id'),
            scheduled_delivery_date: $request->input('scheduled_delivery_date'),
        );
    }
}
