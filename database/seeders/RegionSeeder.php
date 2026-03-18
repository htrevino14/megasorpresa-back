<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $regions = [
            [
                'name' => 'México',
                'code' => 'MX',
                'flag_emoji' => '🇲🇽',
                'flag_url' => null,
                'locale' => 'es_MX',
                'currency_code' => 'MXN',
                'sort_order' => 1,
                'is_active' => true,
                'is_default' => true,
            ],
            [
                'name' => 'Estados Unidos',
                'code' => 'US',
                'flag_emoji' => '🇺🇸',
                'flag_url' => null,
                'locale' => 'en_US',
                'currency_code' => 'USD',
                'sort_order' => 2,
                'is_active' => true,
                'is_default' => false,
            ],
            [
                'name' => 'España',
                'code' => 'ES',
                'flag_emoji' => '🇪🇸',
                'flag_url' => null,
                'locale' => 'es_ES',
                'currency_code' => 'EUR',
                'sort_order' => 3,
                'is_active' => false,
                'is_default' => false,
            ],
        ];

        foreach ($regions as $region) {
            Region::firstOrCreate(['code' => $region['code']], $region);
        }
    }
}
