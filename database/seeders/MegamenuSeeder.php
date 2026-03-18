<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MegamenuCategory;
use App\Models\MegamenuPromoPanel;
use App\Models\MegamenuSubcategoryGroup;
use App\Models\MegamenuSubcategoryItem;
use Illuminate\Database\Seeder;

class MegamenuSeeder extends Seeder
{
    public function run(): void
    {
        $menuData = [
            [
                'name' => 'Juguetes',
                'slug' => 'juguetes',
                'icon' => '🧸',
                'sort_order' => 1,
                'category_slug' => 'juguetes',
                'groups' => [
                    [
                        'title' => 'Por tipo',
                        'sort_order' => 1,
                        'category_slug' => 'juguetes',
                        'items' => [
                            ['label' => 'Muñecas', 'sort_order' => 1, 'category_slug' => 'munecas'],
                            ['label' => 'Carritos y Vehículos', 'sort_order' => 2, 'category_slug' => 'carritos-vehiculos'],
                            ['label' => 'Figuras de Acción', 'sort_order' => 3, 'category_slug' => 'figuras-accion'],
                            ['label' => 'Juguetes de Madera', 'sort_order' => 4, 'category_slug' => 'juguetes-madera'],
                        ],
                    ],
                    [
                        'title' => 'Educativos',
                        'sort_order' => 2,
                        'category_slug' => 'juguetes-educativos',
                        'items' => [
                            ['label' => 'Juguetes Educativos', 'sort_order' => 1, 'category_slug' => 'juguetes-educativos'],
                        ],
                    ],
                ],
                'promo' => [
                    'badge' => '¡Nuevo!',
                    'title' => 'Colección 2025',
                    'description' => 'Descubre los juguetes más innovadores del año',
                    'emoji' => '🎁',
                    'bg_color' => '#FFF3E0',
                    'link_text' => 'Ver colección',
                    'link_url' => '/catalog/juguetes',
                    'image_url' => null,
                ],
            ],
            [
                'name' => 'Juegos de Mesa',
                'slug' => 'juegos-de-mesa',
                'icon' => '🎲',
                'sort_order' => 2,
                'category_slug' => 'juegos-de-mesa',
                'groups' => [
                    [
                        'title' => 'Por tipo',
                        'sort_order' => 1,
                        'category_slug' => 'juegos-de-mesa',
                        'items' => [
                            ['label' => 'Clásicos', 'sort_order' => 1, 'category_slug' => 'juegos-clasicos'],
                            ['label' => 'Estrategia', 'sort_order' => 2, 'category_slug' => 'juegos-estrategia'],
                            ['label' => 'Cartas', 'sort_order' => 3, 'category_slug' => 'juegos-cartas'],
                            ['label' => 'Rompecabezas', 'sort_order' => 4, 'category_slug' => 'rompecabezas'],
                        ],
                    ],
                ],
                'promo' => [
                    'badge' => 'Oferta',
                    'title' => 'Noches de familia',
                    'description' => 'Juegos para disfrutar en familia',
                    'emoji' => '🎮',
                    'bg_color' => '#E3F2FD',
                    'link_text' => 'Ver juegos',
                    'link_url' => '/catalog/juegos-de-mesa',
                    'image_url' => null,
                ],
            ],
            [
                'name' => 'Arte y Manualidades',
                'slug' => 'arte-manualidades',
                'icon' => '🎨',
                'sort_order' => 3,
                'category_slug' => 'arte-manualidades',
                'groups' => [
                    [
                        'title' => 'Materiales',
                        'sort_order' => 1,
                        'category_slug' => 'arte-manualidades',
                        'items' => [
                            ['label' => 'Pintura', 'sort_order' => 1, 'category_slug' => 'pintura'],
                            ['label' => 'Plastilina y Arcilla', 'sort_order' => 2, 'category_slug' => 'plastilina-arcilla'],
                            ['label' => 'Kits de Manualidades', 'sort_order' => 3, 'category_slug' => 'kits-manualidades'],
                        ],
                    ],
                ],
                'promo' => null,
            ],
            [
                'name' => 'Bebés',
                'slug' => 'bebes',
                'icon' => '👶',
                'sort_order' => 4,
                'category_slug' => 'bebes-ninos-pequenos',
                'groups' => [
                    [
                        'title' => 'Para bebés',
                        'sort_order' => 1,
                        'category_slug' => 'bebes-ninos-pequenos',
                        'items' => [
                            ['label' => 'Sonajeros', 'sort_order' => 1, 'category_slug' => 'sonajeros-mordedores'],
                            ['label' => 'Estimulación', 'sort_order' => 2, 'category_slug' => 'juguetes-estimulacion'],
                            ['label' => 'Peluches', 'sort_order' => 3, 'category_slug' => 'peluches'],
                        ],
                    ],
                ],
                'promo' => null,
            ],
        ];

        foreach ($menuData as $menuItem) {
            $groups = $menuItem['groups'];
            $promoData = $menuItem['promo'];
            $categorySlug = $menuItem['category_slug'];
            unset($menuItem['groups'], $menuItem['promo'], $menuItem['category_slug']);

            $destinationCategory = Category::where('slug', $categorySlug)->first();

            $megamenuCategory = MegamenuCategory::firstOrCreate(
                ['slug' => $menuItem['slug']],
                array_merge($menuItem, [
                    'category_id_destination' => $destinationCategory?->id,
                    'is_active' => true,
                ])
            );

            foreach ($groups as $groupData) {
                $groupItems = $groupData['items'];
                $groupCategorySlug = $groupData['category_slug'];
                unset($groupData['items'], $groupData['category_slug']);

                $groupDestCategory = Category::where('slug', $groupCategorySlug)->first();

                $group = MegamenuSubcategoryGroup::firstOrCreate(
                    [
                        'megamenu_category_id' => $megamenuCategory->id,
                        'title' => $groupData['title'],
                    ],
                    array_merge($groupData, [
                        'megamenu_category_id' => $megamenuCategory->id,
                        'category_id_destination' => $groupDestCategory?->id,
                    ])
                );

                foreach ($groupItems as $itemData) {
                    $itemCategorySlug = $itemData['category_slug'];
                    unset($itemData['category_slug']);

                    $itemDestCategory = Category::where('slug', $itemCategorySlug)->first();

                    MegamenuSubcategoryItem::firstOrCreate(
                        [
                            'megamenu_subcategory_group_id' => $group->id,
                            'label' => $itemData['label'],
                        ],
                        array_merge($itemData, [
                            'megamenu_subcategory_group_id' => $group->id,
                            'category_id_destination' => $itemDestCategory?->id,
                        ])
                    );
                }
            }

            if ($promoData !== null && ! $megamenuCategory->promoPanel()->exists()) {
                MegamenuPromoPanel::create(array_merge($promoData, [
                    'megamenu_category_id' => $megamenuCategory->id,
                ]));
            }
        }
    }
}
