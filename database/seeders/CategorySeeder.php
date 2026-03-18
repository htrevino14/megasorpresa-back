<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Juguetes',
                'slug' => 'juguetes',
                'description' => 'Todos los juguetes para niños de todas las edades',
                'image_url' => null,
                'children' => [
                    ['name' => 'Muñecas', 'slug' => 'munecas'],
                    ['name' => 'Carritos y Vehículos', 'slug' => 'carritos-vehiculos'],
                    ['name' => 'Figuras de Acción', 'slug' => 'figuras-accion'],
                    ['name' => 'Juguetes de Madera', 'slug' => 'juguetes-madera'],
                    ['name' => 'Juguetes Educativos', 'slug' => 'juguetes-educativos'],
                ],
            ],
            [
                'name' => 'Juegos de Mesa',
                'slug' => 'juegos-de-mesa',
                'description' => 'Juegos para toda la familia',
                'image_url' => null,
                'children' => [
                    ['name' => 'Juegos Clásicos', 'slug' => 'juegos-clasicos'],
                    ['name' => 'Juegos de Estrategia', 'slug' => 'juegos-estrategia'],
                    ['name' => 'Juegos de Cartas', 'slug' => 'juegos-cartas'],
                    ['name' => 'Rompecabezas', 'slug' => 'rompecabezas'],
                ],
            ],
            [
                'name' => 'Arte y Manualidades',
                'slug' => 'arte-manualidades',
                'description' => 'Materiales para expresar la creatividad',
                'image_url' => null,
                'children' => [
                    ['name' => 'Pintura', 'slug' => 'pintura'],
                    ['name' => 'Plastilina y Arcilla', 'slug' => 'plastilina-arcilla'],
                    ['name' => 'Kits de Manualidades', 'slug' => 'kits-manualidades'],
                ],
            ],
            [
                'name' => 'Juguetes de Exterior',
                'slug' => 'juguetes-exterior',
                'description' => 'Diversión al aire libre',
                'image_url' => null,
                'children' => [
                    ['name' => 'Bicicletas y Patines', 'slug' => 'bicicletas-patines'],
                    ['name' => 'Pelotas y Deportes', 'slug' => 'pelotas-deportes'],
                    ['name' => 'Juegos de Agua', 'slug' => 'juegos-agua'],
                ],
            ],
            [
                'name' => 'Bebés y Niños Pequeños',
                'slug' => 'bebes-ninos-pequenos',
                'description' => 'Juguetes para los más pequeños',
                'image_url' => null,
                'children' => [
                    ['name' => 'Sonajeros y Mordedores', 'slug' => 'sonajeros-mordedores'],
                    ['name' => 'Juguetes de Estimulación', 'slug' => 'juguetes-estimulacion'],
                    ['name' => 'Peluches', 'slug' => 'peluches'],
                ],
            ],
            [
                'name' => 'Electrónica y Tecnología',
                'slug' => 'electronica-tecnologia',
                'description' => 'Juguetes tecnológicos e interactivos',
                'image_url' => null,
                'children' => [
                    ['name' => 'Robots y Drones', 'slug' => 'robots-drones'],
                    ['name' => 'Consolas y Videojuegos', 'slug' => 'consolas-videojuegos'],
                    ['name' => 'Juguetes Interactivos', 'slug' => 'juguetes-interactivos'],
                ],
            ],
        ];

        foreach ($categories as $data) {
            $children = $data['children'] ?? [];
            unset($data['children']);

            $parent = Category::firstOrCreate(
                ['slug' => $data['slug']],
                $data
            );

            foreach ($children as $child) {
                Category::firstOrCreate(
                    ['slug' => $child['slug']],
                    array_merge($child, ['parent_id' => $parent->id])
                );
            }
        }
    }
}
