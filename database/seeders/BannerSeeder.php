<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title' => 'Envío gratis en compras mayores a $500',
                'image_url' => 'https://via.placeholder.com/1200x400?text=Envio+Gratis',
                'link_to' => '/catalog',
                'location' => 'home',
                'is_active' => true,
            ],
            [
                'title' => 'Día del Niño - Hasta 30% de descuento',
                'image_url' => 'https://via.placeholder.com/1200x400?text=Dia+del+Nino',
                'link_to' => '/catalog?promo=dia-nino',
                'location' => 'home',
                'is_active' => true,
            ],
            [
                'title' => 'Nuevos juguetes educativos',
                'image_url' => 'https://via.placeholder.com/1200x400?text=Juguetes+Educativos',
                'link_to' => '/catalog/juguetes-educativos',
                'location' => 'catalog',
                'is_active' => true,
            ],
            [
                'title' => 'Promo de temporada - descuentos especiales',
                'image_url' => 'https://via.placeholder.com/1200x400?text=Promo+Temporada',
                'link_to' => null,
                'location' => 'sidebar',
                'is_active' => false,
            ],
        ];

        foreach ($banners as $banner) {
            Banner::firstOrCreate(['title' => $banner['title']], $banner);
        }
    }
}
