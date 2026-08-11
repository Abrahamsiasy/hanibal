<?php

namespace App\Http\Resources\V1;

use App\Models\CityEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    /**
     * @return array{
     *     id: int,
     *     title: string,
     *     description: string|null,
     *     banner: string|null,
     *     participants: array<int, array{name: string, image: string|null}>|null,
     *     status: string,
     *     starts_at: string|null,
     *     city_event_id: int|null,
     *     is_settled: bool|null,
     *     betting_options: mixed,
     * }
     */
    public function toArray(Request $request): array
    {
        /** @var CityEvent|null $cityEvent */
        $cityEvent = $this->whenLoaded('cityEvents', fn () => $this->cityEvents->first());

        $participants = collect($this->participants ?? [])->map(fn (array $p) => [
            'name' => $p['name'],
            'image' => ! empty($p['image']) ? asset('storage/'.$p['image']) : null,
        ])->values()->all();

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'banner' => $this->image ? asset('storage/'.$this->image) : null,
            'participants' => $participants,
            'status' => $this->status->value,
            'starts_at' => $this->starts_at?->toISOString(),
            'city_event_id' => $cityEvent?->id,
            'is_settled' => $cityEvent?->isSettled(),
            'betting_options' => BettingOptionResource::collection(
                $this->whenLoaded('cityEvents', fn () => $cityEvent?->bettingOptions ?? collect())
            ),
        ];
    }
}
