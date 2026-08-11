<?php

use App\Enums\EventStatus;
use App\Models\BettingOption;
use App\Models\City;
use App\Models\CityEvent;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

it('creates an event', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.events.store'), [
            'title' => 'Abel vs Dawit',
            'description' => 'A match',
            'starts_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'status' => EventStatus::Open->value,
        ])
        ->assertRedirect(route('admin.events.index'));

    $this->assertDatabaseHas('events', [
        'title' => 'Abel vs Dawit',
        'status' => EventStatus::Open->value,
    ]);
});

it('assigns cities without recreating existing city events', function () {
    $event = Event::factory()->create();
    $addis = City::factory()->create(['slug' => 'addis']);
    $gambella = City::factory()->create(['slug' => 'gambella']);
    $existing = CityEvent::factory()->create([
        'city_id' => $addis->id,
        'event_id' => $event->id,
    ]);
    BettingOption::factory()->create([
        'city_event_id' => $existing->id,
        'name' => 'Abel Wins',
        'odds' => 1.80,
    ]);

    $this->actingAs($this->admin)
        ->put(route('admin.events.cities.update', $event), [
            'cities' => [$addis->id, $gambella->id],
        ])
        ->assertRedirect(route('admin.events.cities.edit', $event));

    expect(CityEvent::query()->where('event_id', $event->id)->count())->toBe(2)
        ->and($existing->fresh())->not->toBeNull()
        ->and(BettingOption::query()->where('city_event_id', $existing->id)->exists())->toBeTrue();
});

it('removes city event and its betting options when city is unchecked', function () {
    $event = Event::factory()->create();
    $city = City::factory()->create();
    $cityEvent = CityEvent::factory()->create([
        'city_id' => $city->id,
        'event_id' => $event->id,
    ]);
    BettingOption::factory()->create(['city_event_id' => $cityEvent->id]);

    $this->actingAs($this->admin)
        ->put(route('admin.events.cities.update', $event), [
            'cities' => [],
        ])
        ->assertRedirect(route('admin.events.cities.edit', $event));

    $this->assertDatabaseMissing('city_events', ['id' => $cityEvent->id]);
    $this->assertDatabaseMissing('betting_options', ['city_event_id' => $cityEvent->id]);
});

it('stores city-specific betting options', function () {
    $cityEvent = CityEvent::factory()->create();

    $this->actingAs($this->admin)
        ->post(route('admin.city-events.options.store', $cityEvent), [
            'name' => 'Abel Wins',
            'odds' => 2.50,
            'position' => 1,
            'active' => '1',
        ])
        ->assertRedirect(route('admin.city-events.options.index', $cityEvent));

    $this->assertDatabaseHas('betting_options', [
        'city_event_id' => $cityEvent->id,
        'name' => 'Abel Wins',
        'odds' => 2.50,
    ]);
});
