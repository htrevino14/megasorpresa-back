<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            [
                'name' => 'Tarjeta de crédito o débito',
                'logo_url' => null,
                'icon_class' => 'icon-card',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'PayPal',
                'logo_url' => null,
                'icon_class' => 'icon-paypal',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Transferencia bancaria (SPEI)',
                'logo_url' => null,
                'icon_class' => 'icon-bank',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'OXXO Pay',
                'logo_url' => null,
                'icon_class' => 'icon-oxxo',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Mercado Pago',
                'logo_url' => null,
                'icon_class' => 'icon-mercadopago',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Apple Pay',
                'logo_url' => null,
                'icon_class' => 'icon-applepay',
                'sort_order' => 6,
                'is_active' => false,
            ],
        ];

        foreach ($methods as $method) {
            PaymentMethod::firstOrCreate(['name' => $method['name']], $method);
        }
    }
}
