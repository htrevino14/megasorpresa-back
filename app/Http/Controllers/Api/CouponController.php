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
     * @OA\Post(
     *     path="/api/coupons/validate",
     *     summary="Validar un cupón de descuento",
     *     description="Valida un código de cupón y calcula el descuento aplicable según el subtotal proporcionado. Verifica que el cupón sea válido, no esté expirado y cumpla con los requisitos mínimos.",
     *     tags={"Cupones"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code","subtotal"},
     *             @OA\Property(property="code", type="string", example="PROMO2026", description="Código del cupón a validar"),
     *             @OA\Property(property="subtotal", type="number", format="float", example=150.00, description="Subtotal de la orden antes del descuento", minimum=0)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cupón validado exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="valid", type="boolean", example=true),
     *             @OA\Property(property="discount_type", type="string", example="percentage", enum={"percentage","fixed"}),
     *             @OA\Property(property="discount_value", type="number", format="float", example=10.00),
     *             @OA\Property(property="discount_amount", type="number", format="float", example=15.00, description="Monto del descuento calculado"),
     *             @OA\Property(property="message", type="string", example="Cupón válido")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado",
     *         @OA\JsonContent(ref="#/components/schemas/UnauthorizedError")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Cupón inválido o datos de validación incorrectos",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(ref="#/components/schemas/ValidationError"),
     *                 @OA\Schema(
     *                     type="object",
     *                     @OA\Property(property="valid", type="boolean", example=false),
     *                     @OA\Property(property="message", type="string", example="Cupón expirado o inválido")
     *                 )
     *             }
     *         )
     *     )
     * )
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
