<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
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
     *     slug: string,
     *     hero_title: string|null,
     *     hero_subtitle: string|null,
     *     hero_image: string|null,
     *     banners: mixed,
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'hero_title' => $this->hero_title,
            'hero_subtitle' => $this->hero_subtitle,
            'hero_image' => $this->hero_image ? asset('storage/'.$this->hero_image) : null,
            'banners' => CityBannerResource::collection($this->whenLoaded('banners')),
        ];
    }
}
