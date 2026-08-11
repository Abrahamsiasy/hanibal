<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EventStatus;
use App\Enums\WalletRequestStatus;
use App\Enums\WalletRequestType;
use App\Http\Controllers\Controller;
use App\Models\Bet;
use App\Models\City;
use App\Models\Event;
use App\Models\User;
use App\Models\WalletRequest;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'totalCities' => City::query()->count(),
            'totalEvents' => Event::query()->count(),
            'openEvents' => Event::query()->where('status', EventStatus::Open)->count(),
            'totalCustomers' => User::query()->where('is_admin', false)->count(),
            'pendingDeposits' => WalletRequest::query()
                ->where('status', WalletRequestStatus::Pending)
                ->where('type', WalletRequestType::Deposit)
                ->count(),
            'pendingWithdrawals' => WalletRequest::query()
                ->where('status', WalletRequestStatus::Pending)
                ->where('type', WalletRequestType::Withdrawal)
                ->count(),
            'totalBets' => Bet::query()->count(),
            'totalStaked' => number_format((float) Bet::query()->sum('stake'), 2),
        ]);
    }
}
