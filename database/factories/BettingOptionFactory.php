<?php

namespace Database\Factories;

use App\Models\BettingOption;
use App\Models\CityEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BettingOption>
 */
class BettingOptionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'city_event_id' => CityEvent::factory(),
            'name' => fake()->words(2, true),
            'odds' => fake()->randomFloat(2, 1.10, 5.00),
            'active' => true,
            'position' => fake()->numberBetween(0, 10),
        ];
    }
}
