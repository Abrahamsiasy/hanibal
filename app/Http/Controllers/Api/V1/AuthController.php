<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserResource;
use App\Models\City;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
        ]);

        $city = City::query()->whereKey($validated['city_id'])->where('active', true)->first();

        if (! $city) {
            return response()->json([
                'message' => 'Selected city is not available.',
                'errors' => ['city_id' => ['Selected city is not available.']],
            ], 422);
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

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'message' => 'Account created successfully.',
            'token' => $token,
            'user' => UserResource::make($user->load('city')),
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'phone' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'phone' => ['These credentials do not match our records.'],
            ]);
        }

        /** @var User $user */
        $user = Auth::user();

        if ($user->is_admin) {
            Auth::logout();

            throw ValidationException::withMessages([
                'phone' => ['Admin accounts cannot use the customer API.'],
            ]);
        }

        $user->ensureWallet();
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'message' => 'Logged in successfully.',
            'token' => $token,
            'user' => UserResource::make($user->load('city')),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('city');
        $wallet = $user->ensureWallet();

        return response()->json([
            'user' => UserResource::make($user),
            'wallet' => [
                'balance' => $wallet->balance,
                'available_balance' => $wallet->availableBalance(),
            ],
        ]);
    }
}
