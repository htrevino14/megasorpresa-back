<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'coupon_code' => 'nullable|string|exists:coupons,code',
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'delivery_date' => 'required|date|after:now',
            'delivery_slot_id' => 'nullable|integer|exists:delivery_slots,id',
            'card_message' => 'nullable|string|max:500',
            'payment_method' => 'nullable|string|in:cash,card,transfer',
        ];
    }
}
