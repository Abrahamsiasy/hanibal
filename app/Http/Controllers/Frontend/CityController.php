<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\EventStatus;
use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\View\View;

class CityController extends Controller
{
    public function index(): View
    {
        $cities = City::query()->where('active', true)->orderBy('name')->get();

        return view('frontend.cities.index', compact('cities'));
    }

    public function show(City $city): View
    {
        abort_unless($city->active, 404);

        $city->load(['banners' => fn ($q) => $q->where('active', true)->orderBy('position')]);

        $cityEvents = $city->cityEvents()
            ->where('active', true)
            ->whereHas('event', function ($query) {
                $query->where('status', EventStatus::Open)
                    ->where('starts_at', '>=', now());
            })
            ->with([
                'event.bout.redFighter',
                'event.bout.blueFighter',
                'bettingOptions' => fn ($query) => $query->where('active', true)->orderBy('position')->orderBy('id'),
            ])
            ->get()
            ->sortBy(fn ($cityEvent) => $cityEvent->event->starts_at)
            ->values();

        $mainEvent = $cityEvents->first(fn ($ce) => $ce->event->bout?->is_main_event);
        $fightCard = $cityEvents->reject(fn ($ce) => $ce->event->bout?->is_main_event)->values();

        return view('frontend.cities.show', compact('city', 'cityEvents', 'mainEvent', 'fightCard'));
    }
}
