<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Reference / static data (no dependencies)
            RegionSeeder::class,
            OrderStatusSeeder::class,
            PaymentMethodSeeder::class,
            SocialLinkSeeder::class,
            FooterSeeder::class,
            AnnouncementBarSeeder::class,
            HeroSlideSeeder::class,
            NewsletterSeeder::class,

            // Location hierarchy (states → cities → delivery slots)
            StateSeeder::class,
            CitySeeder::class,
            DeliverySlotSeeder::class,

            // Product catalog (categories → products → images + availability)
            CategorySeeder::class,
            ProductAddonSeeder::class,
            ProductSeeder::class,

            // Megamenu & UI components (depend on categories)
            MegamenuSeeder::class,
            CategoryCarouselItemSeeder::class,
            AgeGroupSeeder::class,

            // Users (depend on cities)
            UserSeeder::class,

            // Promotions
            BannerSeeder::class,
            CouponSeeder::class,

            // Orders (depend on users, products, order statuses, delivery slots)
            OrderSeeder::class,

            // Reviews (depend on products and users)
            ReviewSeeder::class,
        ]);
    }
}
