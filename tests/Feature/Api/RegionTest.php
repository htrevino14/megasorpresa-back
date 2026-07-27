<?php

use App\Models\Region;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lists only active regions ordered by sort order', function () {
    Region::factory()->create(['name' => 'México', 'code' => 'MX', 'sort_order' => 2, 'is_active' => true]);
    Region::factory()->create(['name' => 'Estados Unidos', 'code' => 'US', 'sort_order' => 1, 'is_active' => true]);
    Region::factory()->create(['name' => 'España', 'code' => 'ES', 'sort_order' => 3, 'is_active' => false]);

    $response = $this->getJson('/api/regions');

    $response->assertStatus(200)
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure([
            'data' => [
                ['id', 'name', 'code', 'flag_emoji', 'flag_url', 'locale', 'currency_code', 'is_default', 'sort_order'],
            ],
        ]);

    expect($response->json('data.0.code'))->toBe('US')
        ->and($response->json('data.1.code'))->toBe('MX');
});

it('returns an empty collection when there are no active regions', function () {
    Region::factory()->create(['is_active' => false]);

    $this->getJson('/api/regions')
        ->assertStatus(200)
        ->assertJsonCount(0, 'data');
});
