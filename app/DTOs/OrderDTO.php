<?php

namespace App\DTOs;

use Illuminate\Http\Request;

readonly class OrderDTO
{
    public function __construct(
        public int $user_id,
        public array $items,
        public ?string $coupon_code,
        public string $recipient_name,
        public string $recipient_phone,
        public string $delivery_date,
        public ?int $delivery_slot_id,
        public ?string $card_message,
        public ?string $payment_method,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            user_id: $request->user()->id,
            items: $request->input('items', []),
            coupon_code: $request->input('coupon_code'),
            recipient_name: $request->input('recipient_name'),
            recipient_phone: $request->input('recipient_phone'),
            delivery_date: $request->input('delivery_date'),
            delivery_slot_id: $request->input('delivery_slot_id'),
            card_message: $request->input('card_message'),
            payment_method: $request->input('payment_method'),
        );
    }
}
