<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ProductAddon;
use Illuminate\Database\Seeder;

class ProductAddonSeeder extends Seeder
{
    public function run(): void
    {
        $addons = [
            ['name' => 'Envoltorio de regalo', 'price' => 25.00],
            ['name' => 'Tarjeta personalizada', 'price' => 15.00],
            ['name' => 'Moño decorativo', 'price' => 10.00],
            ['name' => 'Caja premium', 'price' => 50.00],
            ['name' => 'Bolsa de regalo', 'price' => 20.00],
            ['name' => 'Papel de china decorativo', 'price' => 12.00],
            ['name' => 'Listón personalizado', 'price' => 8.00],
            ['name' => 'Globo de cumpleaños', 'price' => 30.00],
        ];

        foreach ($addons as $addon) {
            ProductAddon::firstOrCreate(['name' => $addon['name']], $addon);
        }
    }
}
