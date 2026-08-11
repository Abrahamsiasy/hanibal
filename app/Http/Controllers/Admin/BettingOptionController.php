<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BettingOption;
use App\Models\CityEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BettingOptionController extends Controller
{
    public function index(CityEvent $cityEvent): View
    {
        $cityEvent->load(['city', 'event']);

        $options = $cityEvent->bettingOptions()
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        return view('admin.betting-options.index', compact('cityEvent', 'options'));
    }

    public function store(Request $request, CityEvent $cityEvent): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'odds' => ['required', 'numeric', 'gt:1'],
            'position' => ['nullable', 'integer', 'min:0'],
            'active' => ['sometimes', 'boolean'],
        ]);

        $cityEvent->bettingOptions()->create([
            'name' => $validated['name'],
            'odds' => $validated['odds'],
            'position' => $validated['position'] ?? 0,
            'active' => $request->boolean('active', true),
        ]);

        return redirect()
            ->route('admin.city-events.options.index', $cityEvent)
            ->with('success', 'Betting option added successfully.');
    }

    public function edit(BettingOption $bettingOption): View
    {
        $bettingOption->load(['cityEvent.city', 'cityEvent.event']);

        return view('admin.betting-options.edit', compact('bettingOption'));
    }

    public function update(Request $request, BettingOption $bettingOption): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'odds' => ['required', 'numeric', 'gt:1'],
            'position' => ['nullable', 'integer', 'min:0'],
            'active' => ['sometimes', 'boolean'],
        ]);

        $bettingOption->update([
            'name' => $validated['name'],
            'odds' => $validated['odds'],
            'position' => $validated['position'] ?? 0,
            'active' => $request->boolean('active'),
        ]);

        return redirect()
            ->route('admin.city-events.options.index', $bettingOption->city_event_id)
            ->with('success', 'Betting option updated successfully.');
    }

    public function destroy(BettingOption $bettingOption): RedirectResponse
    {
        $cityEventId = $bettingOption->city_event_id;
        $bettingOption->delete();

        return redirect()
            ->route('admin.city-events.options.index', $cityEventId)
            ->with('success', 'Betting option deleted successfully.');
    }
}
