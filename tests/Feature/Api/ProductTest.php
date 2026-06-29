<?php

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns the detail of an existing product', function () {
    // Arrange: Crear un producto
    $product = Product::factory()->create([
        'is_active' => true,
        'base_price' => 199.99,
    ]);

    // Act: Solicitar el detalle del producto
    $response = $this->getJson("/api/catalog/products/{$product->id}");

    // Assert: Se obtiene el producto correcto
    $response->assertStatus(200)
        ->assertJsonPath('data.id', $product->id)
        ->assertJsonPath('data.name', $product->name);
});

it('returns 404 when the product does not exist', function () {
    // Act: Solicitar un producto inexistente
    $response = $this->getJson('/api/catalog/products/999999');

    // Assert: Se devuelve 404
    $response->assertStatus(404);
});
