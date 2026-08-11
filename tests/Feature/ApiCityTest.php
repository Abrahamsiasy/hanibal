<?php

use App\Models\BettingOption;
use App\Models\City;
use App\Models\CityBanner;
use App\Models\CityEvent;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('city homepage returns banners and open events', function () {
    $city = City::factory()->create(['active' => true]);
    CityBanner::factory()->count(2)->create(['city_id' => $city->id, 'active' => true]);

    $event = Event::factory()->open()->create();
    $cityEvent = CityEvent::factory()->create(['city_id' => $city->id, 'event_id' => $event->id, 'active' => true]);
    BettingOption::factory()->count(2)->create(['city_event_id' => $cityEvent->id, 'active' => true]);

    $this->getJson("/api/v1/cities/{$city->slug}")
        ->assertOk()
        ->assertJsonStructure([
            'city' => ['id', 'name', 'slug', 'banners'],
            'events' => [['id', 'title', 'status', 'betting_options']],
        ])
        ->assertJsonCount(2, 'city.banners')
        ->assertJsonCount(1, 'events');
});

test('inactive city returns 404', function () {
    $city = City::factory()->inactive()->create();

    $this->getJson("/api/v1/cities/{$city->slug}")->assertNotFound();
});

test('settled events are excluded from city homepage', function () {
    $city = City::factory()->create(['active' => true]);
    $event = Event::factory()->open()->create();
    CityEvent::factory()->create([
        'city_id' => $city->id,
        'event_id' => $event->id,
        'active' => true,
        'settled_at' => now(),
    ]);

    $this->getJson("/api/v1/cities/{$city->slug}")
        ->assertOk()
        ->assertJsonCount(0, 'events');
});

test('event detail page returns betting options for the city', function () {
    $city = City::factory()->create(['active' => true]);
    $event = Event::factory()->open()->create();
    $cityEvent = CityEvent::factory()->create(['city_id' => $city->id, 'event_id' => $event->id, 'active' => true]);
    BettingOption::factory()->create(['city_event_id' => $cityEvent->id, 'name' => 'Abel Wins', 'odds' => 2.50]);

    $this->getJson("/api/v1/cities/{$city->slug}/events/{$event->id}")
        ->assertOk()
        ->assertJsonPath('event.title', $event->title)
        ->assertJsonCount(1, 'event.betting_options')
        ->assertJsonPath('event.betting_options.0.name', 'Abel Wins');
});
