<?php

namespace Database\Factories;

use App\Enums\BetStatus;
use App\Models\Bet;
use App\Models\BettingOption;
use App\Models\CityEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bet>
 */
class BetFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $stake = 100;
        $odds = 2.00;

        return [
            'user_id' => User::factory(),
            'city_event_id' => CityEvent::factory(),
            'betting_option_id' => BettingOption::factory(),
            'stake' => $stake,
            'odds' => $odds,
            'potential_payout' => $stake * $odds,
            'status' => BetStatus::Pending,
        ];
    }
}
