<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\City;
use App\Models\Reminder;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin/test user
        $admin = User::firstOrCreate(
            ['email' => 'admin@megasorpresa.com'],
            [
                'name' => 'Administrador',
                'first_name' => 'Admin',
                'last_name' => 'MegaSorpresa',
                'email' => 'admin@megasorpresa.com',
                'phone' => '5512345678',
                'password' => Hash::make('password'),
                'loyalty_points' => 500,
                'email_verified_at' => now(),
            ]
        );

        // Create test user
        $testUser = User::firstOrCreate(
            ['email' => 'test@megasorpresa.com'],
            [
                'name' => 'Usuario de Prueba',
                'first_name' => 'Juan',
                'last_name' => 'García',
                'email' => 'test@megasorpresa.com',
                'phone' => '5598765432',
                'password' => Hash::make('password'),
                'loyalty_points' => 150,
                'email_verified_at' => now(),
            ]
        );

        // Create additional sample users
        $sampleUsers = [
            [
                'name' => 'María López',
                'first_name' => 'María',
                'last_name' => 'López',
                'email' => 'maria@example.com',
                'phone' => '5511223344',
                'password' => Hash::make('password'),
                'loyalty_points' => 75,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Carlos Hernández',
                'first_name' => 'Carlos',
                'last_name' => 'Hernández',
                'email' => 'carlos@example.com',
                'phone' => '5544332211',
                'password' => Hash::make('password'),
                'loyalty_points' => 200,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Ana Martínez',
                'first_name' => 'Ana',
                'last_name' => 'Martínez',
                'email' => 'ana@example.com',
                'phone' => '5566778899',
                'password' => Hash::make('password'),
                'loyalty_points' => 0,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($sampleUsers as $userData) {
            User::firstOrCreate(['email' => $userData['email']], $userData);
        }

        // Add addresses for test user
        $city = City::first();
        if ($city && $testUser->addresses()->count() === 0) {
            UserAddress::create([
                'user_id' => $testUser->id,
                'street' => 'Av. Insurgentes Sur 1234',
                'ext_number' => 'Depto 5',
                'neighborhood' => 'Del Valle',
                'city_id' => $city->id,
                'zip_code' => '03100',
                'references' => 'Entre Filadelfia y Moras, edificio blanco',
            ]);
        }

        // Add reminders for test user
        if ($testUser->reminders()->count() === 0) {
            Reminder::create([
                'user_id' => $testUser->id,
                'event_name' => 'Cumpleaños de mi hijo',
                'date' => now()->addMonths(2)->format('Y-m-d'),
                'notify_days_before' => 7,
            ]);

            Reminder::create([
                'user_id' => $testUser->id,
                'event_name' => 'Navidad',
                'date' => now()->year . '-12-25',
                'notify_days_before' => 30,
            ]);
        }
    }
}
