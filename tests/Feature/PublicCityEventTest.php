<?php

use App\Enums\EventStatus;
use App\Models\BettingOption;
use App\Models\City;
use App\Models\CityEvent;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows open upcoming events with city-specific odds', function () {
    $addis = City::factory()->create(['slug' => 'addis', 'active' => true]);
    $gambella = City::factory()->create(['slug' => 'gambella', 'active' => true]);
    $event = Event::factory()->open()->create(['title' => 'Abel vs Dawit']);

    $addisEvent = CityEvent::factory()->create([
        'city_id' => $addis->id,
        'event_id' => $event->id,
    ]);
    $gambellaEvent = CityEvent::factory()->create([
        'city_id' => $gambella->id,
        'event_id' => $event->id,
    ]);

    BettingOption::factory()->create([
        'city_event_id' => $addisEvent->id,
        'name' => 'Abel Wins',
        'odds' => 1.80,
        'active' => true,
    ]);
    BettingOption::factory()->create([
        'city_event_id' => $gambellaEvent->id,
        'name' => 'Abel Wins',
        'odds' => 2.50,
        'active' => true,
    ]);

    $this->get(route('cities.show', $addis))
        ->assertSuccessful()
        ->assertSee('Abel vs Dawit')
        ->assertSee('1.80')
        ->assertDontSee('2.50');

    $this->get(route('cities.show', $gambella))
        ->assertSuccessful()
        ->assertSee('Abel vs Dawit')
        ->assertSee('2.50')
        ->assertDontSee('1.80');
});

it('returns 404 for inactive cities', function () {
    $city = City::factory()->inactive()->create(['slug' => 'inactive-city']);

    $this->get(route('cities.show', $city))->assertNotFound();
});

it('returns 404 when event is not available in the city', function () {
    $city = City::factory()->create(['slug' => 'addis', 'active' => true]);
    $event = Event::factory()->open()->create();

    $this->get(route('cities.events.show', [$city, $event]))->assertNotFound();
});

it('shows an event page for a city that has the event', function () {
    $city = City::factory()->create([
        'name' => 'Gambella',
        'slug' => 'gambella',
        'active' => true,
    ]);
    $event = Event::factory()->create([
        'title' => 'Abel vs Dawit',
        'status' => EventStatus::Open,
        'description' => 'Big fight',
    ]);
    $cityEvent = CityEvent::factory()->create([
        'city_id' => $city->id,
        'event_id' => $event->id,
        'active' => true,
    ]);
    BettingOption::factory()->create([
        'city_event_id' => $cityEvent->id,
        'name' => 'Draw',
        'odds' => 3.20,
    ]);

    $this->get(route('cities.events.show', [$city, $event]))
        ->assertSuccessful()
        ->assertSee('Abel vs Dawit')
        ->assertSee('Big fight')
        ->assertSee('Gambella')
        ->assertSee('3.20');
});
