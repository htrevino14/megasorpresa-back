<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SocialLink;
use Illuminate\Database\Seeder;

class SocialLinkSeeder extends Seeder
{
    public function run(): void
    {
        $socialLinks = [
            [
                'platform' => 'Facebook',
                'url' => 'https://facebook.com/megasorpresa',
                'icon_class' => 'icon-facebook',
                'icon_svg' => null,
                'initial' => 'F',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'platform' => 'Instagram',
                'url' => 'https://instagram.com/megasorpresa',
                'icon_class' => 'icon-instagram',
                'icon_svg' => null,
                'initial' => 'I',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'platform' => 'TikTok',
                'url' => 'https://tiktok.com/@megasorpresa',
                'icon_class' => 'icon-tiktok',
                'icon_svg' => null,
                'initial' => 'T',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'platform' => 'YouTube',
                'url' => 'https://youtube.com/@megasorpresa',
                'icon_class' => 'icon-youtube',
                'icon_svg' => null,
                'initial' => 'Y',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'platform' => 'WhatsApp',
                'url' => 'https://wa.me/5212345678901',
                'icon_class' => 'icon-whatsapp',
                'icon_svg' => null,
                'initial' => 'W',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'platform' => 'Pinterest',
                'url' => 'https://pinterest.com/megasorpresa',
                'icon_class' => 'icon-pinterest',
                'icon_svg' => null,
                'initial' => 'P',
                'sort_order' => 6,
                'is_active' => false,
            ],
        ];

        foreach ($socialLinks as $link) {
            SocialLink::firstOrCreate(['platform' => $link['platform']], $link);
        }
    }
}
