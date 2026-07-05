<?php

use App\Models\AgeGroup;
use App\Models\AnnouncementBar;
use App\Models\FooterLink;
use App\Models\FooterSection;
use App\Models\HeroSlide;
use App\Models\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns the most recent active announcement bar', function () {
    // Arrange: una barra activa y una inactiva
    $active = AnnouncementBar::factory()->create(['is_active' => true]);
    AnnouncementBar::factory()->create(['is_active' => false]);

    // Act
    $response = $this->getJson('/api/landing/announcement-bar');

    // Assert: sólo se devuelve la barra activa
    $response->assertStatus(200)
        ->assertJsonPath('data.id', $active->id)
        ->assertJsonPath('data.message', $active->message);
});

it('returns null when there is no active announcement bar', function () {
    // Arrange: sólo una barra inactiva
    AnnouncementBar::factory()->create(['is_active' => false]);

    // Act
    $response = $this->getJson('/api/landing/announcement-bar');

    // Assert
    $response->assertStatus(200)
        ->assertJsonPath('data', null);
});

it('returns active hero slides ordered by sort_order', function () {
    // Arrange: dos activos (desordenados) y uno inactivo
    $second = HeroSlide::factory()->create(['is_active' => true, 'sort_order' => 2]);
    $first = HeroSlide::factory()->create(['is_active' => true, 'sort_order' => 1]);
    HeroSlide::factory()->create(['is_active' => false, 'sort_order' => 0]);

    // Act
    $response = $this->getJson('/api/landing/hero-slides');

    // Assert: sólo los activos, en orden ascendente por sort_order
    $response->assertStatus(200)
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.id', $first->id)
        ->assertJsonPath('data.1.id', $second->id);
});

it('returns active age groups', function () {
    // Arrange
    AgeGroup::factory()->create(['is_active' => true]);
    AgeGroup::factory()->create(['is_active' => false]);

    // Act
    $response = $this->getJson('/api/landing/age-groups');

    // Assert: sólo el grupo activo
    $response->assertStatus(200)
        ->assertJsonCount(1, 'data');
});

it('returns footer data with active sections, links and payment methods', function () {
    // Arrange: sección con un enlace activo y uno inactivo
    $section = FooterSection::factory()->create(['is_active' => true]);
    FooterLink::factory()->create([
        'footer_section_id' => $section->id,
        'is_active' => true,
    ]);
    FooterLink::factory()->create([
        'footer_section_id' => $section->id,
        'is_active' => false,
    ]);

    // Sección inactiva que no debe aparecer
    FooterSection::factory()->create(['is_active' => false]);

    // Métodos de pago activo e inactivo
    PaymentMethod::factory()->create(['is_active' => true]);
    PaymentMethod::factory()->create(['is_active' => false]);

    // Act
    $response = $this->getJson('/api/landing/footer');

    // Assert: estructura del footer con filtrado por is_active
    $response->assertStatus(200)
        ->assertJsonCount(1, 'data.sections')
        ->assertJsonPath('data.sections.0.id', $section->id)
        ->assertJsonCount(1, 'data.sections.0.links')
        ->assertJsonCount(1, 'data.payment_methods');
});
