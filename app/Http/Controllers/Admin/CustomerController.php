<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = User::query()
            ->where('is_admin', false)
            ->with(['city', 'wallet'])
            ->withCount('bets')
            ->latest()
            ->paginate(25);

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $user): View
    {
        abort_if($user->is_admin, 404);

        $user->load('city');

        $wallet = $user->ensureWallet();

        $transactions = $wallet->transactions()
            ->latest()
            ->paginate(20, ['*'], 'transactions_page');

        $requests = $user->walletRequests()
            ->latest()
            ->paginate(15, ['*'], 'requests_page');

        $bets = $user->bets()
            ->with(['cityEvent.city', 'cityEvent.event', 'bettingOption'])
            ->latest()
            ->paginate(15, ['*'], 'bets_page');

        return view('admin.customers.show', compact('user', 'wallet', 'transactions', 'requests', 'bets'));
    }
}
