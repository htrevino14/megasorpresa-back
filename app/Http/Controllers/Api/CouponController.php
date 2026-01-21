<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ValidateCouponRequest;
use App\Services\CouponService;

class CouponController extends Controller
{
    public function __construct(
        private CouponService $couponService
    ) {}

    /**
     * Validate a coupon code.
     */
    public function validate(ValidateCouponRequest $request)
    {
        $result = $this->couponService->validateCoupon(
            $request->input('code'),
            $request->input('subtotal')
        );

        return response()->json($result);
    }
}
