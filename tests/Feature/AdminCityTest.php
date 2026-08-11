<?php

use App\Models\City;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

it('lists cities for admins', function () {
    City::factory()->create(['name' => 'Jimma', 'slug' => 'jimma']);

    $this->actingAs($this->admin)
        ->get(route('admin.cities.index'))
        ->assertSuccessful()
        ->assertSee('Jimma');
});

it('creates a city', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.cities.store'), [
            'name' => 'Bahir Dar',
            'slug' => 'bahir-dar',
            'hero_title' => 'Welcome',
            'hero_subtitle' => 'Play local',
            'active' => '1',
        ])
        ->assertRedirect(route('admin.cities.index'));

    $this->assertDatabaseHas('cities', [
        'name' => 'Bahir Dar',
        'slug' => 'bahir-dar',
        'active' => true,
    ]);
});

it('updates a city', function () {
    $city = City::factory()->create(['slug' => 'old-slug']);

    $this->actingAs($this->admin)
        ->put(route('admin.cities.update', $city), [
            'name' => 'Updated City',
            'slug' => 'updated-city',
            'active' => '1',
        ])
        ->assertRedirect(route('admin.cities.index'));

    expect($city->fresh()->slug)->toBe('updated-city');
});

it('deletes a city', function () {
    $city = City::factory()->create(['slug' => 'to-delete']);

    $this->actingAs($this->admin)
        ->delete(route('admin.cities.destroy', $city))
        ->assertRedirect(route('admin.cities.index'));

    $this->assertDatabaseMissing('cities', ['id' => $city->id]);
});
