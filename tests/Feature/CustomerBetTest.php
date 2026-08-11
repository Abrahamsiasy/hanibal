<?php

use App\Enums\BetStatus;
use App\Enums\EventStatus;
use App\Models\BettingOption;
use App\Models\City;
use App\Models\CityEvent;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('places a bet and deducts stake from the wallet', function () {
    $city = City::factory()->create(['slug' => 'gambella', 'active' => true]);
    $user = User::factory()->create([
        'is_admin' => false,
        'city_id' => $city->id,
    ]);
    $wallet = $user->wallet()->create(['balance' => 500]);

    $event = Event::factory()->open()->create();
    $cityEvent = CityEvent::factory()->create([
        'city_id' => $city->id,
        'event_id' => $event->id,
        'active' => true,
    ]);
    $option = BettingOption::factory()->create([
        'city_event_id' => $cityEvent->id,
        'name' => 'Abel Wins',
        'odds' => 2.50,
        'active' => true,
    ]);

    $this->actingAs($user)
        ->post(route('cities.events.bets.store', [$city, $event]), [
            'betting_option_id' => $option->id,
            'stake' => 100,
        ])
        ->assertRedirect(route('bets.index'));

    $this->assertDatabaseHas('bets', [
        'user_id' => $user->id,
        'betting_option_id' => $option->id,
        'stake' => 100.00,
        'odds' => 2.50,
        'potential_payout' => 250.00,
        'status' => BetStatus::Pending->value,
    ]);

    expect((float) $wallet->fresh()->balance)->toBe(400.0);
});

it('prevents betting in a different city', function () {
    $homeCity = City::factory()->create(['slug' => 'addis', 'active' => true]);
    $otherCity = City::factory()->create(['slug' => 'gambella', 'active' => true]);
    $user = User::factory()->create([
        'is_admin' => false,
        'city_id' => $homeCity->id,
    ]);
    $user->wallet()->create(['balance' => 500]);

    $event = Event::factory()->open()->create();
    $cityEvent = CityEvent::factory()->create([
        'city_id' => $otherCity->id,
        'event_id' => $event->id,
    ]);
    $option = BettingOption::factory()->create([
        'city_event_id' => $cityEvent->id,
        'odds' => 2.00,
    ]);

    $this->actingAs($user)
        ->from(route('cities.events.show', [$otherCity, $event]))
        ->post(route('cities.events.bets.store', [$otherCity, $event]), [
            'betting_option_id' => $option->id,
            'stake' => 50,
        ])
        ->assertRedirect(route('cities.events.show', [$otherCity, $event]))
        ->assertSessionHasErrors('stake');
});

it('settles bets when admin selects a winning option', function () {
    $admin = User::factory()->admin()->create();
    $city = City::factory()->create(['active' => true]);
    $winner = User::factory()->create(['is_admin' => false, 'city_id' => $city->id]);
    $loser = User::factory()->create(['is_admin' => false, 'city_id' => $city->id]);
    $winnerWallet = $winner->wallet()->create(['balance' => 0]);
    $loserWallet = $loser->wallet()->create(['balance' => 0]);

    $event = Event::factory()->create(['status' => EventStatus::Closed]);
    $cityEvent = CityEvent::factory()->create([
        'city_id' => $city->id,
        'event_id' => $event->id,
    ]);
    $winningOption = BettingOption::factory()->create([
        'city_event_id' => $cityEvent->id,
        'name' => 'Abel Wins',
        'odds' => 2.00,
    ]);
    $losingOption = BettingOption::factory()->create([
        'city_event_id' => $cityEvent->id,
        'name' => 'Dawit Wins',
        'odds' => 3.00,
    ]);

    $winner->bets()->create([
        'city_event_id' => $cityEvent->id,
        'betting_option_id' => $winningOption->id,
        'stake' => 100,
        'odds' => 2.00,
        'potential_payout' => 200,
        'status' => BetStatus::Pending,
    ]);
    $loser->bets()->create([
        'city_event_id' => $cityEvent->id,
        'betting_option_id' => $losingOption->id,
        'stake' => 50,
        'odds' => 3.00,
        'potential_payout' => 150,
        'status' => BetStatus::Pending,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.city-events.settle.update', $cityEvent), [
            'winning_betting_option_id' => $winningOption->id,
        ])
        ->assertRedirect(route('admin.events.cities.edit', $event));

    expect($cityEvent->fresh()->isSettled())->toBeTrue()
        ->and($winner->bets()->first()->status)->toBe(BetStatus::Won)
        ->and($loser->bets()->first()->status)->toBe(BetStatus::Lost)
        ->and((float) $winnerWallet->fresh()->balance)->toBe(200.0)
        ->and((float) $loserWallet->fresh()->balance)->toBe(0.0);
});
