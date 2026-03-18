<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\City;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AvailabilityZone>
 */
class AvailabilityZoneFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'city_id' => City::factory(),
        ];
    }
}
