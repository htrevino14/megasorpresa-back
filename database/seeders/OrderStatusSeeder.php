<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            'Pendiente',
            'Procesando',
            'Enviado',
            'Entregado',
            'Cancelado',
            'Reembolsado',
        ];

        foreach ($statuses as $name) {
            OrderStatus::firstOrCreate(['name' => $name]);
        }
    }
}
