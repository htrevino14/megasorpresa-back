<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\City;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserAddress>
 */
class UserAddressFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'street' => fake()->streetAddress(),
            'ext_number' => fake()->optional(0.8)->buildingNumber(),
            'neighborhood' => fake()->optional(0.7)->citySuffix(),
            'city_id' => City::factory(),
            'zip_code' => fake()->optional(0.9)->postcode(),
            'references' => fake()->optional(0.5)->sentence(),
        ];
    }
}
