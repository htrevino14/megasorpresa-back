<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\NewsletterCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class NewsletterSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'label' => 'Novedades y lanzamientos',
                'slug' => 'novedades-lanzamientos',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'label' => 'Ofertas y promociones',
                'slug' => 'ofertas-promociones',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'label' => 'Tips y consejos para padres',
                'slug' => 'tips-consejos-padres',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'label' => 'Juguetes educativos',
                'slug' => 'juguetes-educativos-newsletter',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'label' => 'Temporada navideña',
                'slug' => 'temporada-navidena',
                'sort_order' => 5,
                'is_active' => false,
            ],
        ];

        foreach ($categories as $category) {
            NewsletterCategory::firstOrCreate(['slug' => $category['slug']], $category);
        }

        // Subscribe test user to some newsletter categories
        $testUser = User::where('email', 'test@megasorpresa.com')->first();
        if ($testUser) {
            $activeCategories = NewsletterCategory::where('is_active', true)->take(2)->get();
            foreach ($activeCategories as $category) {
                if (! $testUser->newsletterCategories()->where('newsletter_category_id', $category->id)->exists()) {
                    $testUser->newsletterCategories()->attach($category->id, [
                        'subscribed_at' => now(),
                    ]);
                }
            }
        }
    }
}
