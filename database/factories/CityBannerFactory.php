<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\CityBanner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CityBanner>
 */
class CityBannerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'city_id' => City::factory(),
            'title' => fake()->sentence(4),
            'subtitle' => fake()->sentence(8),
            'image' => null,
            'link' => null,
            'active' => true,
            'position' => fake()->numberBetween(0, 10),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => ['active' => false]);
    }
}
