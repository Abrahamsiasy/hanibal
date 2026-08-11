<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\CityEvent;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CityEvent>
 */
class CityEventFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'city_id' => City::factory(),
            'event_id' => Event::factory(),
            'active' => true,
        ];
    }
}
