<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\CityEvent;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventCityController extends Controller
{
    public function edit(Event $event): View
    {
        $cities = City::query()->orderBy('name')->get();
        $cityEvents = $event->cityEvents()->with('city')->get();
        $selectedCityIds = $cityEvents->pluck('city_id')->all();

        return view('admin.events.cities', compact('event', 'cities', 'cityEvents', 'selectedCityIds'));
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'cities' => ['nullable', 'array'],
            'cities.*' => ['integer', 'exists:cities,id'],
        ]);

        $selectedCityIds = collect($validated['cities'] ?? [])->map(fn ($id) => (int) $id)->unique()->values();
        $existingCityIds = $event->cityEvents()->pluck('city_id');

        $cityIdsToRemove = $existingCityIds->diff($selectedCityIds);
        $cityIdsToAdd = $selectedCityIds->diff($existingCityIds);

        if ($cityIdsToRemove->isNotEmpty()) {
            CityEvent::query()
                ->whereBelongsTo($event)
                ->whereIn('city_id', $cityIdsToRemove)
                ->each(fn (CityEvent $cityEvent) => $cityEvent->delete());
        }

        foreach ($cityIdsToAdd as $cityId) {
            CityEvent::query()->create([
                'city_id' => $cityId,
                'event_id' => $event->id,
                'active' => true,
            ]);
        }

        return redirect()
            ->route('admin.events.cities.edit', $event)
            ->with('success', 'Event cities updated successfully.');
    }
}
