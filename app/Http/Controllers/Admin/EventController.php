<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EventStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $events = Event::query()
            ->withCount('cities')
            ->latest()
            ->paginate(15);

        return view('admin.events.index', compact('events'));
    }

    public function create(): View
    {
        return view('admin.events.create', [
            'statuses' => EventStatus::cases(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'starts_at' => ['required', 'date'],
            'status' => ['required', Rule::enum(EventStatus::class)],
            'participant_names' => ['nullable', 'array', 'max:4'],
            'participant_names.*' => ['nullable', 'string', 'max:100'],
            'participant_images' => ['nullable', 'array', 'max:4'],
            'participant_images.*' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        $validated['participants'] = $this->buildParticipants($request, []);

        Event::query()->create($validated);

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event created successfully.');
    }

    public function show(Event $event): View
    {
        $event->load(['cities', 'cityEvents.city']);

        return view('admin.events.show', compact('event'));
    }

    public function edit(Event $event): View
    {
        return view('admin.events.edit', [
            'event' => $event,
            'statuses' => EventStatus::cases(),
        ]);
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'starts_at' => ['required', 'date'],
            'status' => ['required', Rule::enum(EventStatus::class)],
            'participant_names' => ['nullable', 'array', 'max:4'],
            'participant_names.*' => ['nullable', 'string', 'max:100'],
            'participant_images' => ['nullable', 'array', 'max:4'],
            'participant_images.*' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('image')) {
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }

            $validated['image'] = $request->file('image')->store('events', 'public');
        }

        $validated['participants'] = $this->buildParticipants($request, $event->participants ?? []);

        $event->update($validated);

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }

        foreach ($event->participants ?? [] as $participant) {
            if (! empty($participant['image'])) {
                Storage::disk('public')->delete($participant['image']);
            }
        }

        $event->delete();

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event deleted successfully.');
    }

    /**
     * Build participants array, merging existing images with new uploads.
     *
     * @param  array<int, array{name: string, image: string|null}>  $existing
     * @return array<int, array{name: string, image: string|null}>
     */
    private function buildParticipants(Request $request, array $existing): array
    {
        $names = $request->input('participant_names', []);
        $participants = [];

        foreach ($names as $i => $name) {
            if (empty($name)) {
                continue;
            }

            $image = $existing[$i]['image'] ?? null;

            if ($request->hasFile("participant_images.{$i}")) {
                if ($image) {
                    Storage::disk('public')->delete($image);
                }
                $image = $request->file("participant_images.{$i}")->store('participants', 'public');
            }

            $participants[] = ['name' => $name, 'image' => $image];
        }

        return $participants;
    }
}
