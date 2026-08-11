<?php

namespace Database\Factories;

use App\Enums\EventStatus;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'image' => null,
            'starts_at' => now()->addDays(fake()->numberBetween(1, 14)),
            'status' => EventStatus::Draft,
        ];
    }

    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EventStatus::Open,
            'starts_at' => now()->addDays(3),
        ]);
    }
}
