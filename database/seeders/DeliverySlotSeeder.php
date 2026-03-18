<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\City;
use App\Models\DeliverySlot;
use Illuminate\Database\Seeder;

class DeliverySlotSeeder extends Seeder
{
    public function run(): void
    {
        $slots = [
            ['start_time' => '09:00:00', 'end_time' => '13:00:00', 'additional_cost' => 0],
            ['start_time' => '13:00:00', 'end_time' => '17:00:00', 'additional_cost' => 0],
            ['start_time' => '17:00:00', 'end_time' => '21:00:00', 'additional_cost' => 50],
        ];

        $cities = City::where('is_active', true)->get();

        foreach ($cities as $city) {
            foreach ($slots as $slot) {
                DeliverySlot::firstOrCreate(
                    [
                        'city_id' => $city->id,
                        'start_time' => $slot['start_time'],
                        'end_time' => $slot['end_time'],
                    ],
                    [
                        'additional_cost' => $slot['additional_cost'],
                        'capacity_limit' => 20,
                    ]
                );
            }
        }
    }
}
