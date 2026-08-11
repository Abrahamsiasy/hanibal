<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check() && ! Auth::user()?->is_admin) {
            return redirect()->route('wallet.show');
        }

        return view('frontend.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'phone' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('phone'))
                ->withErrors(['phone' => 'These credentials do not match our records.']);
        }

        $request->session()->regenerate();

        if (Auth::user()?->is_admin) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withInput($request->only('phone'))
                ->withErrors(['phone' => 'Please use the admin login.']);
        }

        Auth::user()?->ensureWallet();

        $city = Auth::user()?->city;

        return $city
            ? redirect()->intended(route('cities.show', $city))
            : redirect()->intended(route('wallet.show'));
    }

    public function showRegister(): View
    {
        return view('frontend.auth.register', [
            'cities' => City::query()->where('active', true)->orderBy('name')->get(),
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
        ]);

        $city = City::query()->whereKey($validated['city_id'])->where('active', true)->first();

        if (! $city) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['city_id' => 'Selected city is not available.']);
        }

        $user = DB::transaction(function () use ($validated) {
            $user = User::query()->create([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'password' => $validated['password'],
                'city_id' => $validated['city_id'],
                'is_admin' => false,
            ]);

            $user->wallet()->create(['balance' => 0]);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('cities.show', $user->city)
            ->with('success', 'Account created successfully.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
