<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\CityBanner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CityBannerController extends Controller
{
    public function create(City $city): View
    {
        return view('admin.city-banners.create', compact('city'));
    }

    public function store(Request $request, City $city): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'],
            'link' => ['nullable', 'url', 'max:500'],
            'position' => ['nullable', 'integer', 'min:0'],
            'active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('banners', 'public');
        }

        $city->banners()->create([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'image' => $validated['image'] ?? null,
            'link' => $validated['link'] ?? null,
            'position' => $validated['position'] ?? 0,
            'active' => $request->boolean('active', true),
        ]);

        return redirect()
            ->route('admin.cities.show', $city)
            ->with('success', 'Banner created.');
    }

    public function edit(CityBanner $cityBanner): View
    {
        return view('admin.city-banners.edit', ['banner' => $cityBanner, 'city' => $cityBanner->city]);
    }

    public function update(Request $request, CityBanner $cityBanner): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:2048'],
            'link' => ['nullable', 'url', 'max:500'],
            'position' => ['nullable', 'integer', 'min:0'],
            'active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            if ($cityBanner->image) {
                Storage::disk('public')->delete($cityBanner->image);
            }
            $validated['image'] = $request->file('image')->store('banners', 'public');
        }

        $cityBanner->update([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'image' => $validated['image'] ?? $cityBanner->image,
            'link' => $validated['link'] ?? null,
            'position' => $validated['position'] ?? 0,
            'active' => $request->boolean('active', true),
        ]);

        return redirect()
            ->route('admin.cities.show', $cityBanner->city)
            ->with('success', 'Banner updated.');
    }

    public function destroy(CityBanner $cityBanner): RedirectResponse
    {
        $city = $cityBanner->city;

        if ($cityBanner->image) {
            Storage::disk('public')->delete($cityBanner->image);
        }

        $cityBanner->delete();

        return redirect()
            ->route('admin.cities.show', $city)
            ->with('success', 'Banner deleted.');
    }
}
