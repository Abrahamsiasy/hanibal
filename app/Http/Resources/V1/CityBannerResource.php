<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityBannerResource extends JsonResource
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
     *     subtitle: string|null,
     *     image: string|null,
     *     link: string|null,
     *     position: int,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'image' => $this->image ? asset('storage/'.$this->image) : null,
            'link' => $this->link,
            'position' => $this->position,
        ];
    }
}
