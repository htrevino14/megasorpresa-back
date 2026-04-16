<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\HeroSlide;
use Illuminate\Database\Seeder;

class HeroSlideSeeder extends Seeder
{
    public function run(): void
    {
        $slides = [
            [
                'title' => 'Juguetes que inspiran y educan',
                'subtitle' => 'Encuentra el regalo perfecto para cada niño',
                'cta_text' => 'Ver catálogo',
                'cta_link' => '/catalog',
                'image_url_desktop' => 'https://storage.googleapis.com/megasorpresa_storefront_bucket/carrito.png',
                'image_url_mobile' => 'https://storage.googleapis.com/megasorpresa_storefront_bucket/carrito.png',
                'alt_text' => 'Niños jugando con juguetes de MegaSorpresa',
                'bg_color' => '#FFF8E7',
                'sort_order' => 1,
                'is_active' => true,
                'starts_at' => null,
                'ends_at' => null,
            ],
            [
                'title' => 'Día del Niño - Ofertas especiales',
                'subtitle' => 'Hasta 30% de descuento en juguetes seleccionados',
                'cta_text' => '¡Comprar ahora!',
                'cta_link' => '/catalog?promo=dia-nino',
                'image_url_desktop' => 'https://storage.googleapis.com/megasorpresa_storefront_bucket/lego.png',
                'image_url_mobile' => 'https://storage.googleapis.com/megasorpresa_storefront_bucket/lego.png',
                'alt_text' => 'Promoción Día del Niño',
                'bg_color' => '#FF6B35',
                'sort_order' => 2,
                'is_active' => true,
                'starts_at' => null,
                'ends_at' => now()->addMonths(1),
            ],
            [
                'title' => 'Juguetes Educativos',
                'subtitle' => 'Aprende jugando con nuestra colección de juguetes STEM',
                'cta_text' => 'Explorar',
                'cta_link' => '/catalog/juguetes-educativos',
                'image_url_desktop' => 'https://storage.googleapis.com/megasorpresa_storefront_bucket/futbol.png',
                'image_url_mobile' => 'https://storage.googleapis.com/megasorpresa_storefront_bucket/futbol.png',
                'alt_text' => 'Juguetes educativos STEM',
                'bg_color' => '#E8F5E9',
                'sort_order' => 3,
                'is_active' => true,
                'starts_at' => null,
                'ends_at' => null,
            ],
        ];

        foreach ($slides as $slide) {
            HeroSlide::firstOrCreate(['title' => $slide['title']], $slide);
        }
    }
}
