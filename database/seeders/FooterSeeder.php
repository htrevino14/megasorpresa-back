<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\FooterLink;
use App\Models\FooterSection;
use Illuminate\Database\Seeder;

class FooterSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            [
                'title' => 'MegaSorpresa',
                'sort_order' => 1,
                'links' => [
                    ['label' => 'Nosotros', 'url' => '/about', 'sort_order' => 1],
                    ['label' => 'Blog', 'url' => '/blog', 'sort_order' => 2],
                    ['label' => 'Trabaja con nosotros', 'url' => '/careers', 'sort_order' => 3],
                    ['label' => 'Prensa', 'url' => '/press', 'sort_order' => 4],
                ],
            ],
            [
                'title' => 'Ayuda',
                'sort_order' => 2,
                'links' => [
                    ['label' => 'Centro de ayuda', 'url' => '/help', 'sort_order' => 1],
                    ['label' => 'Seguimiento de pedido', 'url' => '/track-order', 'sort_order' => 2],
                    ['label' => 'Devoluciones', 'url' => '/returns', 'sort_order' => 3],
                    ['label' => 'Contáctanos', 'url' => '/contact', 'sort_order' => 4],
                ],
            ],
            [
                'title' => 'Comprar',
                'sort_order' => 3,
                'links' => [
                    ['label' => 'Catálogo', 'url' => '/catalog', 'sort_order' => 1],
                    ['label' => 'Ofertas', 'url' => '/catalog?sale=true', 'sort_order' => 2],
                    ['label' => 'Novedades', 'url' => '/catalog?new=true', 'sort_order' => 3],
                    ['label' => 'Más vendidos', 'url' => '/catalog?popular=true', 'sort_order' => 4],
                ],
            ],
            [
                'title' => 'Legal',
                'sort_order' => 4,
                'links' => [
                    ['label' => 'Términos y condiciones', 'url' => '/terms', 'sort_order' => 1],
                    ['label' => 'Política de privacidad', 'url' => '/privacy', 'sort_order' => 2],
                    ['label' => 'Política de cookies', 'url' => '/cookies', 'sort_order' => 3],
                ],
            ],
        ];

        foreach ($sections as $sectionData) {
            $links = $sectionData['links'];
            unset($sectionData['links']);

            $section = FooterSection::firstOrCreate(
                ['title' => $sectionData['title']],
                array_merge($sectionData, ['is_active' => true])
            );

            foreach ($links as $link) {
                FooterLink::firstOrCreate(
                    ['footer_section_id' => $section->id, 'label' => $link['label']],
                    array_merge($link, [
                        'footer_section_id' => $section->id,
                        'open_in_new_tab' => false,
                        'is_active' => true,
                    ])
                );
            }
        }
    }
}
