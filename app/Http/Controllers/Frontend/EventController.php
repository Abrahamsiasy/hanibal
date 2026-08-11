<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Event;
use Illuminate\View\View;

class EventController extends Controller
{
    public function show(City $city, Event $event): View
    {
        abort_unless($city->active, 404);

        $cityEvent = $city->cityEvents()
            ->where('event_id', $event->id)
            ->where('active', true)
            ->with([
                'event.bout.redFighter',
                'event.bout.blueFighter',
                'city',
                'bettingOptions' => fn ($query) => $query->where('active', true)->orderBy('position')->orderBy('id'),
            ])
            ->firstOrFail();

        $wallet = auth()->check() ? auth()->user()->ensureWallet() : null;

        return view('frontend.events.show', [
            'city' => $city,
            'event' => $cityEvent->event,
            'cityEvent' => $cityEvent,
            'options' => $cityEvent->bettingOptions,
            'availableBalance' => $wallet?->availableBalance() ?? '0.00',
        ]);
    }
}
