<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('updates the authenticated user profile', function () {
    // Arrange: Crear y autenticar usuario
    $user = User::factory()->create();

    // Act: Actualizar el perfil
    $response = $this->actingAs($user, 'sanctum')
        ->putJson('/api/profile', [
            'first_name' => 'María',
            'last_name' => 'García',
            'gender' => 'Ella',
            'phone_code' => '+52',
            'phone' => '5512345678',
        ]);

    // Assert: La respuesta refleja los datos actualizados
    $response->assertStatus(200)
        ->assertJsonPath('data.first_name', 'María')
        ->assertJsonPath('data.last_name', 'García')
        ->assertJsonPath('data.name', 'María García')
        ->assertJsonPath('data.phone', '+525512345678');

    // Assert: La persistencia ocurrió con el nombre y teléfono concatenados
    expect($user->fresh())
        ->name->toBe('María García')
        ->phone->toBe('+525512345678');
});

it('rejects a profile update with an invalid gender', function () {
    // Arrange: Crear y autenticar usuario
    $user = User::factory()->create();

    // Act: Enviar un género inválido
    $response = $this->actingAs($user, 'sanctum')
        ->putJson('/api/profile', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'gender' => 'Other',
            'phone_code' => '+52',
            'phone' => '5512345678',
        ]);

    // Assert: Se rechaza con error de validación
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['gender']);
});

it('denies updating the profile without authentication', function () {
    // Act: Intentar actualizar sin token
    $response = $this->putJson('/api/profile', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'gender' => 'Él',
        'phone_code' => '+52',
        'phone' => '5512345678',
    ]);

    // Assert: Se rechaza por falta de autenticación
    $response->assertStatus(401);
});
