<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\EventResource;
use App\Models\City;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function show(Request $request, City $city, Event $event): JsonResponse
    {
        abort_unless($city->active, 404);

        $cityEvent = $city->cityEvents()
            ->where('event_id', $event->id)
            ->where('active', true)
            ->with(['bettingOptions' => fn ($q) => $q->where('active', true)->orderBy('position')])
            ->firstOrFail();

        $event->setRelation('cityEvents', collect([$cityEvent]));

        return response()->json([
            'event' => EventResource::make($event),
        ]);
    }
}
