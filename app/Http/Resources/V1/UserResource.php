<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    /**
     * @return array{
     *     id: int,
     *     name: string,
     *     phone: string,
     *     city: CityResource|null,
     *     created_at: string,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'city' => CityResource::make($this->whenLoaded('city')),
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
