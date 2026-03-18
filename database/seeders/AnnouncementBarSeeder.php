<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AnnouncementBar;
use Illuminate\Database\Seeder;

class AnnouncementBarSeeder extends Seeder
{
    public function run(): void
    {
        $bars = [
            [
                'message' => '🚚 ¡Envío gratis en compras mayores a $500! Aplica en ciudades seleccionadas.',
                'link_url' => '/catalog',
                'link_label' => 'Ver productos',
                'bg_color' => '#0072E3',
                'text_color' => '#FFFFFF',
                'is_active' => true,
                'starts_at' => null,
                'ends_at' => null,
            ],
            [
                'message' => '🎉 Día del Niño - Hasta 30% de descuento en juguetes seleccionados',
                'link_url' => '/catalog?promo=dia-nino',
                'link_label' => '¡Aprovecha!',
                'bg_color' => '#FF6B35',
                'text_color' => '#FFFFFF',
                'is_active' => true,
                'starts_at' => null,
                'ends_at' => now()->addMonths(1),
            ],
            [
                'message' => '⭐ Nuevos productos educativos disponibles ahora',
                'link_url' => '/catalog/juguetes-educativos',
                'link_label' => 'Descubrir',
                'bg_color' => '#28A745',
                'text_color' => '#FFFFFF',
                'is_active' => false,
                'starts_at' => null,
                'ends_at' => null,
            ],
        ];

        foreach ($bars as $bar) {
            AnnouncementBar::firstOrCreate(['message' => $bar['message']], $bar);
        }
    }
}
