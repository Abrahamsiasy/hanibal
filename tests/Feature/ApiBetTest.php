<?php

use App\Enums\BetStatus;
use App\Models\BettingOption;
use App\Models\City;
use App\Models\CityEvent;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('customer can place a bet and stake is deducted from wallet', function () {
    $city = City::factory()->create(['active' => true]);
    $user = User::factory()->create(['is_admin' => false, 'city_id' => $city->id]);
    $user->wallet()->create(['balance' => 500]);

    $event = Event::factory()->open()->create();
    $cityEvent = CityEvent::factory()->create(['city_id' => $city->id, 'event_id' => $event->id, 'active' => true]);
    $option = BettingOption::factory()->create(['city_event_id' => $cityEvent->id, 'odds' => 2.50, 'active' => true]);

    $this->actingAs($user, 'sanctum')
        ->postJson("/api/v1/cities/{$city->slug}/events/{$event->id}/bets", [
            'betting_option_id' => $option->id,
            'stake' => 100,
        ])
        ->assertCreated()
        ->assertJsonPath('bet.stake', '100.00')
        ->assertJsonPath('bet.odds', '2.50')
        ->assertJsonPath('bet.potential_payout', '250.00')
        ->assertJsonPath('bet.status', BetStatus::Pending->value);

    expect($user->fresh()->wallet->balance)->toBe('400.00');
});

test('customer cannot bet in another city', function () {
    $city = City::factory()->create(['active' => true]);
    $otherCity = City::factory()->create(['active' => true]);
    $user = User::factory()->create(['is_admin' => false, 'city_id' => $otherCity->id]);
    $user->wallet()->create(['balance' => 500]);

    $event = Event::factory()->open()->create();
    $cityEvent = CityEvent::factory()->create(['city_id' => $city->id, 'event_id' => $event->id, 'active' => true]);
    $option = BettingOption::factory()->create(['city_event_id' => $cityEvent->id, 'active' => true]);

    $this->actingAs($user, 'sanctum')
        ->postJson("/api/v1/cities/{$city->slug}/events/{$event->id}/bets", [
            'betting_option_id' => $option->id,
            'stake' => 100,
        ])
        ->assertForbidden();
});

test('bet fails when insufficient balance', function () {
    $city = City::factory()->create(['active' => true]);
    $user = User::factory()->create(['is_admin' => false, 'city_id' => $city->id]);
    $user->wallet()->create(['balance' => 50]);

    $event = Event::factory()->open()->create();
    $cityEvent = CityEvent::factory()->create(['city_id' => $city->id, 'event_id' => $event->id, 'active' => true]);
    $option = BettingOption::factory()->create(['city_event_id' => $cityEvent->id, 'active' => true]);

    $this->actingAs($user, 'sanctum')
        ->postJson("/api/v1/cities/{$city->slug}/events/{$event->id}/bets", [
            'betting_option_id' => $option->id,
            'stake' => 100,
        ])
        ->assertUnprocessable()
        ->assertJsonPath('errors.stake.0', 'Insufficient available balance.');
});

test('customer can list their bets', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/v1/bets')
        ->assertOk()
        ->assertJsonStructure(['data', 'links', 'meta']);
});
