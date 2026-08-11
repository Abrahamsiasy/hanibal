<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BettingOptionController;
use App\Http\Controllers\Admin\CityBannerController;
use App\Http\Controllers\Admin\CityController as AdminCityController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventCityController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\SettlementController;
use App\Http\Controllers\Admin\WalletRequestController;
use App\Http\Controllers\Frontend\AuthController;
use App\Http\Controllers\Frontend\BetController;
use App\Http\Controllers\Frontend\CityController;
use App\Http\Controllers\Frontend\EventController;
use App\Http\Controllers\Frontend\WalletController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CityController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/c/{city}', [CityController::class, 'show'])->name('cities.show');
Route::get('/c/{city}/events/{event}', [EventController::class, 'show'])->name('cities.events.show');

/*
|--------------------------------------------------------------------------
| Customer Auth
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('register.store');
});

/*
|--------------------------------------------------------------------------
| Customer Protected
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/wallet', [WalletController::class, 'show'])->name('wallet.show');
    Route::post('/wallet/deposit', [WalletController::class, 'storeDeposit'])->name('wallet.deposit');
    Route::post('/wallet/withdraw', [WalletController::class, 'storeWithdrawal'])->name('wallet.withdraw');

    Route::get('/bets', [BetController::class, 'index'])->name('bets.index');
    Route::get('/bets/{bet}', [BetController::class, 'show'])->name('bets.show');
    Route::post('/bets/multi', [BetController::class, 'storeMulti'])->name('bets.multi');
    Route::post('/c/{city}/events/{event}/bets', [BetController::class, 'store'])->name('cities.events.bets.store');
});

/*
|--------------------------------------------------------------------------
| Admin Auth
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AdminAuthController::class, 'login'])->middleware('throttle:5,1')->name('login.store');
    Route::post('logout', [AdminAuthController::class, 'logout'])->middleware('auth')->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Admin Protected
    |--------------------------------------------------------------------------
    */
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('cities', AdminCityController::class);
        Route::resource('events', AdminEventController::class);

        Route::get('events/{event}/cities', [EventCityController::class, 'edit'])->name('events.cities.edit');
        Route::put('events/{event}/cities', [EventCityController::class, 'update'])->name('events.cities.update');

        Route::get('city-events/{cityEvent}/options', [BettingOptionController::class, 'index'])->name('city-events.options.index');
        Route::post('city-events/{cityEvent}/options', [BettingOptionController::class, 'store'])->name('city-events.options.store');

        Route::get('betting-options/{bettingOption}/edit', [BettingOptionController::class, 'edit'])->name('betting-options.edit');
        Route::put('betting-options/{bettingOption}', [BettingOptionController::class, 'update'])->name('betting-options.update');
        Route::delete('betting-options/{bettingOption}', [BettingOptionController::class, 'destroy'])->name('betting-options.destroy');

        Route::get('wallet-requests', [WalletRequestController::class, 'index'])->name('wallet-requests.index');
        Route::post('wallet-requests/{walletRequest}/approve', [WalletRequestController::class, 'approve'])->name('wallet-requests.approve');
        Route::post('wallet-requests/{walletRequest}/reject', [WalletRequestController::class, 'reject'])->name('wallet-requests.reject');

        Route::get('city-events/{cityEvent}/settle', [SettlementController::class, 'edit'])->name('city-events.settle.edit');
        Route::post('city-events/{cityEvent}/settle', [SettlementController::class, 'update'])->name('city-events.settle.update');

        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::get('customers/{user}', [CustomerController::class, 'show'])->name('customers.show');

        Route::get('cities/{city}/banners/create', [CityBannerController::class, 'create'])->name('cities.banners.create');
        Route::post('cities/{city}/banners', [CityBannerController::class, 'store'])->name('cities.banners.store');
        Route::get('city-banners/{cityBanner}/edit', [CityBannerController::class, 'edit'])->name('city-banners.edit');
        Route::put('city-banners/{cityBanner}', [CityBannerController::class, 'update'])->name('city-banners.update');
        Route::delete('city-banners/{cityBanner}', [CityBannerController::class, 'destroy'])->name('city-banners.destroy');
    });
});
