<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BetController;
use App\Http\Controllers\Api\V1\CityController;
use App\Http\Controllers\Api\V1\EventController;
use App\Http\Controllers\Api\V1\WalletController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public
    Route::get('/cities', [CityController::class, 'index'])->name('api.v1.cities.index');
    Route::get('/cities/{city}', [CityController::class, 'show'])->name('api.v1.cities.show');
    Route::get('/cities/{city}/events/{event}', [EventController::class, 'show'])->name('api.v1.cities.events.show');

    // Auth (guest)
    Route::post('/auth/register', [AuthController::class, 'register'])->name('api.v1.auth.register')->middleware('throttle:5,1');
    Route::post('/auth/login', [AuthController::class, 'login'])->name('api.v1.auth.login')->middleware('throttle:5,1');

    // Protected
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.v1.auth.logout');
        Route::get('/auth/me', [AuthController::class, 'me'])->name('api.v1.auth.me');

        Route::get('/wallet', [WalletController::class, 'show'])->name('api.v1.wallet.show');
        Route::post('/wallet/deposit', [WalletController::class, 'deposit'])->name('api.v1.wallet.deposit');
        Route::post('/wallet/withdraw', [WalletController::class, 'withdraw'])->name('api.v1.wallet.withdraw');

        Route::get('/bets', [BetController::class, 'index'])->name('api.v1.bets.index');
        Route::post('/bets/multi', [BetController::class, 'storeMulti'])->name('api.v1.bets.multi');
        Route::post('/cities/{city}/events/{event}/bets', [BetController::class, 'store'])->name('api.v1.cities.events.bets.store');
    });
});
