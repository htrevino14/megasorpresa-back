<?php

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can get or create a cart for guest user', function () {
    // Arrange: Create a product
    $product = Product::factory()->create([
        'is_active' => true,
        'stock_quantity' => 10,
    ]);

    // Act: Get cart (will create one)
    $response = $this->getJson('/api/cart');

    // Assert: Cart was created
    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
                'session_id',
                'items',
                'subtotal',
                'total_items',
            ]
        ]);

    expect($response->json('data.total_items'))->toBe(0);
});

it('can add a product to cart', function () {
    // Arrange: Create a product
    $product = Product::factory()->create([
        'is_active' => true,
        'stock_quantity' => 10,
        'base_price' => 99.99,
    ]);

    // Act: Add product to cart
    $response = $this->postJson('/api/cart/add', [
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    // Assert: Product was added
    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Product added to cart successfully',
        ])
        ->assertJsonPath('data.total_items', 2);
});

it('increments quantity when adding existing product', function () {
    // Arrange: Create a product and add it to cart
    $product = Product::factory()->create([
        'is_active' => true,
        'stock_quantity' => 10,
    ]);

    $this->postJson('/api/cart/add', [
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    // Act: Add the same product again
    $response = $this->postJson('/api/cart/add', [
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    // Assert: Quantity was incremented
    $response->assertStatus(200)
        ->assertJsonPath('data.total_items', 3);
});

it('rejects adding product with insufficient stock', function () {
    // Arrange: Create a product with limited stock
    $product = Product::factory()->create([
        'is_active' => true,
        'stock_quantity' => 2,
    ]);

    // Act: Try to add more than available
    $response = $this->postJson('/api/cart/add', [
        'product_id' => $product->id,
        'quantity' => 5,
    ]);

    // Assert: Request was rejected
    $response->assertStatus(422)
        ->assertJson([
            'message' => 'Failed to add product to cart',
        ]);
});

it('can update cart item quantity', function () {
    // Arrange: Create a product and add it to cart
    $product = Product::factory()->create([
        'is_active' => true,
        'stock_quantity' => 10,
    ]);

    $this->postJson('/api/cart/add', [
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    // Act: Update quantity
    $response = $this->patchJson('/api/cart/update-quantity', [
        'product_id' => $product->id,
        'quantity' => 5,
    ]);

    // Assert: Quantity was updated
    $response->assertStatus(200)
        ->assertJsonPath('data.total_items', 5);
});

it('can remove a product from cart', function () {
    // Arrange: Create products and add them to cart
    $product1 = Product::factory()->create(['is_active' => true, 'stock_quantity' => 10]);
    $product2 = Product::factory()->create(['is_active' => true, 'stock_quantity' => 10]);

    $this->postJson('/api/cart/add', ['product_id' => $product1->id, 'quantity' => 2]);
    $this->postJson('/api/cart/add', ['product_id' => $product2->id, 'quantity' => 1]);

    // Act: Remove first product
    $response = $this->deleteJson("/api/cart/remove/{$product1->id}");

    // Assert: Product was removed
    $response->assertStatus(200)
        ->assertJsonPath('data.total_items', 1);
});

it('can update cart delivery details', function () {
    // Arrange: Create a cart with an item
    $product = Product::factory()->create(['is_active' => true, 'stock_quantity' => 10]);
    $this->postJson('/api/cart/add', ['product_id' => $product->id, 'quantity' => 1]);

    // Act: Update delivery details
    $response = $this->postJson('/api/cart/details', [
        'shipping_zip_code' => '12345',
        'scheduled_delivery_date' => now()->addDays(3)->format('Y-m-d'),
    ]);

    // Assert: Details were updated
    $response->assertStatus(200)
        ->assertJsonPath('data.shipping_zip_code', '12345');
});

it('can clear the cart', function () {
    // Arrange: Add products to cart
    $product1 = Product::factory()->create(['is_active' => true, 'stock_quantity' => 10]);
    $product2 = Product::factory()->create(['is_active' => true, 'stock_quantity' => 10]);

    $this->postJson('/api/cart/add', ['product_id' => $product1->id, 'quantity' => 2]);
    $this->postJson('/api/cart/add', ['product_id' => $product2->id, 'quantity' => 1]);

    // Act: Clear cart
    $response = $this->deleteJson('/api/cart/clear');

    // Assert: Cart is empty
    $response->assertStatus(200)
        ->assertJsonPath('data.total_items', 0);
});

it('maintains separate carts for different sessions', function () {
    // Arrange: Create two products
    $product1 = Product::factory()->create(['is_active' => true, 'stock_quantity' => 10]);
    $product2 = Product::factory()->create(['is_active' => true, 'stock_quantity' => 10]);

    // Act: Add product to first cart
    $response1 = $this->postJson('/api/cart/add', ['product_id' => $product1->id, 'quantity' => 2]);

    // Simulate different session
    $this->session(['_token' => 'different-session']);

    // Add different product to second cart
    $response2 = $this->postJson('/api/cart/add', ['product_id' => $product2->id, 'quantity' => 1]);

    // Assert: Each cart has its own items
    expect($response1->json('data.total_items'))->toBe(2);
    expect($response2->json('data.total_items'))->toBe(1);
});
