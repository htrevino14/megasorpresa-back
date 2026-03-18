<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AgeGroup;
use App\Models\Category;
use Illuminate\Database\Seeder;

class AgeGroupSeeder extends Seeder
{
    public function run(): void
    {
        $ageGroups = [
            [
                'label' => '0-2 años',
                'sublabel' => 'Bebés y Pequeñines',
                'slug' => '0-2-anos',
                'bg_color' => '#FFB3C6',
                'text_color' => '#FFFFFF',
                'sort_order' => 1,
                'category_slug' => 'bebes-ninos-pequenos',
            ],
            [
                'label' => '3-5 años',
                'sublabel' => 'Preescolar',
                'slug' => '3-5-anos',
                'bg_color' => '#FFD166',
                'text_color' => '#333333',
                'sort_order' => 2,
                'category_slug' => 'juguetes',
            ],
            [
                'label' => '6-8 años',
                'sublabel' => 'Primaria Baja',
                'slug' => '6-8-anos',
                'bg_color' => '#06D6A0',
                'text_color' => '#FFFFFF',
                'sort_order' => 3,
                'category_slug' => 'juguetes-educativos',
            ],
            [
                'label' => '9-12 años',
                'sublabel' => 'Primaria Alta',
                'slug' => '9-12-anos',
                'bg_color' => '#118AB2',
                'text_color' => '#FFFFFF',
                'sort_order' => 4,
                'category_slug' => 'juegos-de-mesa',
            ],
            [
                'label' => '12+ años',
                'sublabel' => 'Adolescentes',
                'slug' => '12-mas-anos',
                'bg_color' => '#073B4C',
                'text_color' => '#FFFFFF',
                'sort_order' => 5,
                'category_slug' => 'electronica-tecnologia',
            ],
        ];

        foreach ($ageGroups as $group) {
            $categorySlug = $group['category_slug'];
            unset($group['category_slug']);

            $category = Category::where('slug', $categorySlug)->first();

            AgeGroup::firstOrCreate(
                ['slug' => $group['slug']],
                array_merge($group, [
                    'category_id_destination' => $category?->id,
                    'is_active' => true,
                ])
            );
        }
    }
}
