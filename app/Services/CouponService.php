<?php

namespace App\Services;

use App\Models\Coupon;

class CouponService
{
    /**
     * Validate a coupon code.
     */
    public function validateCoupon(string $code, float $subtotal): array
    {
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return [
                'valid' => false,
                'message' => 'Coupon not found',
                'discount' => 0,
            ];
        }

        if (!$coupon->isValid()) {
            return [
                'valid' => false,
                'message' => 'Coupon has expired',
                'discount' => 0,
            ];
        }

        if ($coupon->min_purchase && $subtotal < $coupon->min_purchase) {
            return [
                'valid' => false,
                'message' => "Minimum purchase of {$coupon->min_purchase} required",
                'discount' => 0,
            ];
        }

        $discount = $coupon->calculateDiscount($subtotal);

        return [
            'valid' => true,
            'message' => 'Coupon applied successfully',
            'discount' => $discount,
            'coupon' => $coupon,
        ];
    }
}
