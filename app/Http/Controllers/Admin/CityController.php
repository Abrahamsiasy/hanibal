<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CityController extends Controller
{
    public function index(): View
    {
        $cities = City::query()
            ->withCount('events')
            ->latest()
            ->paginate(15);

        return view('admin.cities.index', compact('cities'));
    }

    public function create(): View
    {
        return view('admin.cities.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:cities,slug'],
            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string', 'max:255'],
            'hero_image' => ['nullable', 'image', 'max:2048'],
            'venue' => ['nullable', 'string', 'max:255'],
            'active' => ['sometimes', 'boolean'],
        ]);

        $validated['active'] = $request->boolean('active');

        if ($request->hasFile('hero_image')) {
            $validated['hero_image'] = $request->file('hero_image')->store('cities', 'public');
        }

        City::query()->create($validated);

        return redirect()
            ->route('admin.cities.index')
            ->with('success', 'City created successfully.');
    }

    public function show(City $city): View
    {
        $city->loadCount('events');
        $city->load(['banners' => fn ($q) => $q->orderBy('position')]);

        return view('admin.cities.show', compact('city'));
    }

    public function edit(City $city): View
    {
        return view('admin.cities.edit', compact('city'));
    }

    public function update(Request $request, City $city): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('cities', 'slug')->ignore($city->id)],
            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string', 'max:255'],
            'hero_image' => ['nullable', 'image', 'max:2048'],
            'venue' => ['nullable', 'string', 'max:255'],
            'active' => ['sometimes', 'boolean'],
        ]);

        $validated['active'] = $request->boolean('active');

        if ($request->hasFile('hero_image')) {
            if ($city->hero_image) {
                Storage::disk('public')->delete($city->hero_image);
            }

            $validated['hero_image'] = $request->file('hero_image')->store('cities', 'public');
        }

        $city->update($validated);

        return redirect()
            ->route('admin.cities.index')
            ->with('success', 'City updated successfully.');
    }

    public function destroy(City $city): RedirectResponse
    {
        if ($city->hero_image) {
            Storage::disk('public')->delete($city->hero_image);
        }

        $city->delete();

        return redirect()
            ->route('admin.cities.index')
            ->with('success', 'City deleted successfully.');
    }
}
