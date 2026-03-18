<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $citiesByState = [
            'Ciudad de México' => [
                'Ciudad de México',
                'Benito Juárez',
                'Coyoacán',
                'Tlalpan',
                'Xochimilco',
                'Iztapalapa',
            ],
            'Jalisco' => [
                'Guadalajara',
                'Zapopan',
                'San Pedro Tlaquepaque',
                'Tonalá',
                'Tlajomulco de Zúñiga',
            ],
            'Nuevo León' => [
                'Monterrey',
                'San Pedro Garza García',
                'San Nicolás de los Garza',
                'Guadalupe',
                'Apodaca',
            ],
            'Estado de México' => [
                'Ecatepec de Morelos',
                'Nezahualcóyotl',
                'Tlalnepantla de Baz',
                'Naucalpan de Juárez',
                'Toluca',
                'Chimalhuacán',
            ],
            'Puebla' => [
                'Puebla',
                'San Andrés Cholula',
                'San Pedro Cholula',
                'Cuautlancingo',
            ],
            'Guanajuato' => [
                'León',
                'Irapuato',
                'Celaya',
                'Salamanca',
                'Guanajuato',
            ],
            'Querétaro' => [
                'Querétaro',
                'Corregidora',
                'El Marqués',
                'San Juan del Río',
            ],
            'Veracruz' => [
                'Veracruz',
                'Xalapa',
                'Coatzacoalcos',
                'Boca del Río',
            ],
            'Yucatán' => [
                'Mérida',
                'Kanasín',
                'Umán',
                'Valladolid',
            ],
        ];

        foreach ($citiesByState as $stateName => $cities) {
            $state = State::where('name', $stateName)->first();

            if (! $state) {
                continue;
            }

            foreach ($cities as $cityName) {
                City::firstOrCreate(
                    ['state_id' => $state->id, 'name' => $cityName],
                    ['is_active' => true]
                );
            }
        }
    }
}
