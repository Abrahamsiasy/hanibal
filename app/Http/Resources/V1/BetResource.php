<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    /**
     * @return array{
     *     id: int,
     *     event_title: string|null,
     *     city_name: string|null,
     *     option_name: string|null,
     *     stake: string,
     *     odds: string,
     *     potential_payout: string,
     *     status: string,
     *     settled_at: string|null,
     *     placed_at: string,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event_title' => $this->whenLoaded('cityEvent', fn () => $this->cityEvent->event?->title),
            'city_name' => $this->whenLoaded('cityEvent', fn () => $this->cityEvent->city?->name),
            'option_name' => $this->whenLoaded('bettingOption', fn () => $this->bettingOption->name),
            'stake' => $this->stake,
            'odds' => $this->odds,
            'potential_payout' => $this->potential_payout,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'settled_at' => $this->settled_at?->toISOString(),
            'placed_at' => $this->created_at->toISOString(),
        ];
    }
}
