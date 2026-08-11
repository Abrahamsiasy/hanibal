<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\EventStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CityResource;
use App\Http\Resources\V1\EventResource;
use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CityController extends Controller
{
    public function index(): JsonResponse
    {
        $cities = City::query()
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return response()->json([
            'cities' => CityResource::collection($cities),
        ]);
    }

    public function show(Request $request, City $city): JsonResponse
    {
        abort_unless($city->active, 404);

        $city->load(['banners' => fn ($q) => $q->where('active', true)->orderBy('position')]);

        $events = $city->cityEvents()
            ->where('active', true)
            ->whereNull('settled_at')
            ->whereHas('event', fn ($q) => $q->where('status', EventStatus::Open)->where('starts_at', '>', now()))
            ->with(['event', 'bettingOptions' => fn ($q) => $q->where('active', true)->orderBy('position')])
            ->get()
            ->map(fn ($cityEvent) => $cityEvent->event->setRelation('cityEvents', collect([$cityEvent])));

        return response()->json([
            'city' => CityResource::make($city),
            'events' => EventResource::collection($events),
        ]);
    }
}
