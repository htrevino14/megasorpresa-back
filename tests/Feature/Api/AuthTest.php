<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('can authenticate a user and receive a token', function () {
    // Arrange: Crear un usuario
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    // Act: Intentar login
    $response = $this->postJson('/api/auth/login', [
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    // Assert: Verificar respuesta exitosa
    $response->assertStatus(200)
        ->assertJsonStructure([
            'token',
            'user' => ['id', 'name', 'email']
        ]);

    expect($response->json('user.email'))->toBe('test@example.com');
});

it('rejects login with invalid credentials', function () {
    // Arrange: Crear un usuario
    User::factory()->create([
        'email' => 'auth-test@example.com',
        'password' => Hash::make('password123'),
    ]);

    // Act: Intentar login con contraseña incorrecta
    $response = $this->postJson('/api/auth/login', [
        'email' => 'auth-test@example.com',
        'password' => 'wrongpassword',
    ]);

    // Assert: Verificar que se rechace
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('can access authenticated routes with valid token', function () {
    // Arrange: Crear y autenticar usuario
    $user = User::factory()->create();

    // Act: Hacer petición autenticada
    $response = $this->actingAs($user, 'sanctum')
        ->getJson('/api/user');

    // Assert: Verificar respuesta exitosa
    $response->assertStatus(200)
        ->assertJson([
            'id' => $user->id,
            'email' => $user->email,
        ]);
});

it('denies access to authenticated routes without token', function () {
    // Act: Intentar acceder sin token
    $response = $this->getJson('/api/user');

    // Assert: Verificar que se rechace
    $response->assertStatus(401);
});

it('can logout and revoke token', function () {
    // Arrange: Crear y autenticar usuario
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    // Act: Hacer logout
    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/auth/logout');

    // Assert: Verificar logout exitoso
    $response->assertStatus(200)
        ->assertJson(['message' => 'Token revoked successfully']);

    // Verificar que el token ya no funciona
    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('/api/user');
    
    $response->assertStatus(401);
});
