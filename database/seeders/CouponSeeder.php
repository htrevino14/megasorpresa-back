<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    public function run(): void
    {
        $coupons = [
            [
                'code' => 'BIENVENIDO10',
                'discount_type' => 'percentage',
                'value' => 10.00,
                'min_purchase' => 200.00,
                'expiry_date' => now()->addYear()->format('Y-m-d'),
            ],
            [
                'code' => 'MEGA50',
                'discount_type' => 'fixed',
                'value' => 50.00,
                'min_purchase' => 300.00,
                'expiry_date' => now()->addMonths(6)->format('Y-m-d'),
            ],
            [
                'code' => 'NAVIDAD20',
                'discount_type' => 'percentage',
                'value' => 20.00,
                'min_purchase' => 500.00,
                'expiry_date' => now()->year . '-12-31',
            ],
            [
                'code' => 'NINO15',
                'discount_type' => 'percentage',
                'value' => 15.00,
                'min_purchase' => 150.00,
                'expiry_date' => now()->addMonths(3)->format('Y-m-d'),
            ],
            [
                'code' => 'ENVIOGRATIS',
                'discount_type' => 'fixed',
                'value' => 99.00,
                'min_purchase' => 500.00,
                'expiry_date' => now()->addMonths(2)->format('Y-m-d'),
            ],
            [
                'code' => 'EXPIRED2023',
                'discount_type' => 'percentage',
                'value' => 25.00,
                'min_purchase' => null,
                'expiry_date' => '2023-12-31',
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::firstOrCreate(['code' => $coupon['code']], $coupon);
        }
    }
}
