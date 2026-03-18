<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\CategoryCarouselItem;
use Illuminate\Database\Seeder;

class CategoryCarouselItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'name' => 'Muñecas',
                'slug' => 'munecas',
                'image_url' => 'https://via.placeholder.com/200x200?text=Munecas',
                'bg_color' => '#FFE0E6',
                'sort_order' => 1,
                'category_slug' => 'munecas',
            ],
            [
                'name' => 'Carritos',
                'slug' => 'carritos',
                'image_url' => 'https://via.placeholder.com/200x200?text=Carritos',
                'bg_color' => '#E3F2FD',
                'sort_order' => 2,
                'category_slug' => 'carritos-vehiculos',
            ],
            [
                'name' => 'Juegos de Mesa',
                'slug' => 'juegos-mesa',
                'image_url' => 'https://via.placeholder.com/200x200?text=Juegos+Mesa',
                'bg_color' => '#FFF8E1',
                'sort_order' => 3,
                'category_slug' => 'juegos-de-mesa',
            ],
            [
                'name' => 'Arte',
                'slug' => 'arte',
                'image_url' => 'https://via.placeholder.com/200x200?text=Arte',
                'bg_color' => '#F3E5F5',
                'sort_order' => 4,
                'category_slug' => 'arte-manualidades',
            ],
            [
                'name' => 'Exterior',
                'slug' => 'exterior',
                'image_url' => 'https://via.placeholder.com/200x200?text=Exterior',
                'bg_color' => '#E8F5E9',
                'sort_order' => 5,
                'category_slug' => 'juguetes-exterior',
            ],
            [
                'name' => 'Bebés',
                'slug' => 'bebes',
                'image_url' => 'https://via.placeholder.com/200x200?text=Bebes',
                'bg_color' => '#FFF3E0',
                'sort_order' => 6,
                'category_slug' => 'bebes-ninos-pequenos',
            ],
            [
                'name' => 'Tecnología',
                'slug' => 'tecnologia',
                'image_url' => 'https://via.placeholder.com/200x200?text=Tecnologia',
                'bg_color' => '#E8EAF6',
                'sort_order' => 7,
                'category_slug' => 'electronica-tecnologia',
            ],
            [
                'name' => 'Educativos',
                'slug' => 'educativos',
                'image_url' => 'https://via.placeholder.com/200x200?text=Educativos',
                'bg_color' => '#E0F7FA',
                'sort_order' => 8,
                'category_slug' => 'juguetes-educativos',
            ],
        ];

        foreach ($items as $item) {
            $categorySlug = $item['category_slug'];
            unset($item['category_slug']);

            $category = Category::where('slug', $categorySlug)->first();

            CategoryCarouselItem::firstOrCreate(
                ['slug' => $item['slug']],
                array_merge($item, [
                    'category_id' => $category?->id,
                    'is_active' => true,
                ])
            );
        }
    }
}
