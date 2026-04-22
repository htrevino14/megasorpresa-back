<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AvailabilityZone;
use App\Models\Category;
use App\Models\City;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Muñeca Barbie Fashionista',
                'slug' => 'muneca-barbie-fashionista',
                'sku' => 'TOY-0001-MBF',
                'base_price' => 299.00,
                'description' => 'Barbie con ropa de moda y accesorios intercambiables. Perfecta para niñas de 3 años en adelante.',
                'stock_quantity' => 50,
                'is_active' => true,
                'category' => 'munecas',
            ],
            [
                'name' => 'Carro de Control Remoto Pro',
                'slug' => 'carro-control-remoto-pro',
                'sku' => 'TOY-0002-CRP',
                'base_price' => 599.00,
                'description' => 'Carro de control remoto de alta velocidad con batería recargable. Alcanza hasta 30 km/h.',
                'stock_quantity' => 30,
                'is_active' => true,
                'category' => 'carritos-vehiculos',
            ],
            [
                'name' => 'LEGO Ciudad Policial',
                'slug' => 'lego-ciudad-policial',
                'sku' => 'TOY-0003-LCP',
                'base_price' => 799.00,
                'description' => 'Set de construcción LEGO con estación de policía, patrullas y 4 minifiguras.',
                'stock_quantity' => 40,
                'is_active' => true,
                'category' => 'juguetes-educativos',
            ],
            [
                'name' => 'Rompecabezas 1000 Piezas México',
                'slug' => 'rompecabezas-1000-piezas-mexico',
                'sku' => 'TOY-0004-RPM',
                'base_price' => 199.00,
                'description' => 'Rompecabezas con mapa de México y sus estados. Educativo y divertido para toda la familia.',
                'stock_quantity' => 60,
                'is_active' => true,
                'category' => 'rompecabezas',
            ],
            [
                'name' => 'Pelota de Fútbol Oficial',
                'slug' => 'pelota-futbol-oficial',
                'sku' => 'TOY-0005-PFO',
                'base_price' => 249.00,
                'description' => 'Pelota de fútbol tamaño oficial, ideal para jugar en jardín o cancha.',
                'stock_quantity' => 80,
                'is_active' => true,
                'category' => 'pelotas-deportes',
            ],
            [
                'name' => 'Osito de Peluche Suave',
                'slug' => 'osito-peluche-suave',
                'sku' => 'TOY-0006-OPS',
                'base_price' => 149.00,
                'description' => 'Tierno osito de peluche súper suave, hipoalergénico y seguro para bebés.',
                'stock_quantity' => 100,
                'is_active' => true,
                'category' => 'peluches',
            ],
            [
                'name' => 'Robot Programable Educativo',
                'slug' => 'robot-programable-educativo',
                'sku' => 'TOY-0007-RPE',
                'base_price' => 1299.00,
                'description' => 'Robot interactivo que enseña programación básica a niños de 6 a 12 años.',
                'stock_quantity' => 20,
                'is_active' => true,
                'category' => 'robots-drones',
            ],
            [
                'name' => 'Kit de Pintura Acuarela',
                'slug' => 'kit-pintura-acuarela',
                'sku' => 'TOY-0008-KPA',
                'base_price' => 189.00,
                'description' => 'Set completo de acuarelas con 24 colores, 3 pinceles y bloc de dibujo.',
                'stock_quantity' => 45,
                'is_active' => true,
                'category' => 'pintura',
            ],
            [
                'name' => 'Juego de Mesa Monopoly Edición México',
                'slug' => 'monopoly-edicion-mexico',
                'sku' => 'TOY-0009-MMX',
                'base_price' => 449.00,
                'description' => 'Versión del clásico Monopoly con las ciudades y monumentos más famosos de México.',
                'stock_quantity' => 35,
                'is_active' => true,
                'category' => 'juegos-clasicos',
            ],
            [
                'name' => 'Drone para Niños con Cámara',
                'slug' => 'drone-ninos-camara',
                'sku' => 'TOY-0010-DNC',
                'base_price' => 899.00,
                'description' => 'Drone fácil de manejar con cámara HD. Incluye protectores y batería extra.',
                'stock_quantity' => 15,
                'is_active' => true,
                'category' => 'robots-drones',
            ],
            [
                'name' => 'Sonajero Musical para Bebé',
                'slug' => 'sonajero-musical-bebe',
                'sku' => 'TOY-0011-SMB',
                'base_price' => 89.00,
                'description' => 'Sonajero colorido con melodías suaves. Estimula los sentidos del bebé.',
                'stock_quantity' => 70,
                'is_active' => true,
                'category' => 'sonajeros-mordedores',
            ],
            [
                'name' => 'Plastilina Set Creativo 20 Colores',
                'slug' => 'plastilina-set-creativo-20-colores',
                'sku' => 'TOY-0012-PSC',
                'base_price' => 129.00,
                'description' => 'Set de plastilina con 20 colores vibrantes y moldes incluidos. No tóxica.',
                'stock_quantity' => 55,
                'is_active' => true,
                'category' => 'plastilina-arcilla',
            ],
        ];

        $cities = City::where('is_active', true)->pluck('id')->toArray();

        foreach ($products as $data) {
            $categorySlug = $data['category'];
            unset($data['category']);

            $product = Product::firstOrCreate(
                ['sku' => $data['sku']],
                $data
            );

            // Assign category
            $category = Category::where('slug', $categorySlug)->first();
            if ($category && ! $product->categories()->where('category_id', $category->id)->exists()) {
                $product->categories()->attach($category->id);
            }

            // Create primary image if none exists
            if ($product->images()->count() === 0) {
                ProductImage::create([
                    'product_id' => $product->id,
                    //'url' => 'https://via.placeholder.com/800x600?text=' . urlencode($product->name),
                    'url' => 'https://storage.googleapis.com/megasorpresa_storefront_bucket/carrito.png',
                    'is_primary' => true,
                    'order' => 0,
                ]);
            }

            // Assign availability to first 3 cities if no zones exist
            if ($product->availabilityZones()->count() === 0 && count($cities) > 0) {
                $assignedCities = array_slice($cities, 0, min(3, count($cities)));
                foreach ($assignedCities as $cityId) {
                    AvailabilityZone::firstOrCreate([
                        'product_id' => $product->id,
                        'city_id' => $cityId,
                    ]);
                }
            }
        }
    }
}
