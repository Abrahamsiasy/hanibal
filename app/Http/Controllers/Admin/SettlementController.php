<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BetStatus;
use App\Enums\WalletTransactionType;
use App\Http\Controllers\Controller;
use App\Models\Bet;
use App\Models\BettingOption;
use App\Models\CityEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class SettlementController extends Controller
{
    public function edit(CityEvent $cityEvent): View
    {
        $cityEvent->load([
            'city',
            'event',
            'winningBettingOption',
            'bettingOptions' => fn ($query) => $query->orderBy('position')->orderBy('id'),
        ]);

        $pendingBetsCount = $cityEvent->bets()->where('status', BetStatus::Pending)->count();

        return view('admin.settlements.edit', compact('cityEvent', 'pendingBetsCount'));
    }

    public function update(Request $request, CityEvent $cityEvent): RedirectResponse
    {
        $validated = $request->validate([
            'winning_betting_option_id' => ['required', 'integer', 'exists:betting_options,id'],
        ]);

        if ($cityEvent->isSettled()) {
            return back()->withErrors(['winning_betting_option_id' => 'This city event is already settled.']);
        }

        $winningOption = BettingOption::query()
            ->whereKey($validated['winning_betting_option_id'])
            ->where('city_event_id', $cityEvent->id)
            ->first();

        if (! $winningOption) {
            return back()->withErrors(['winning_betting_option_id' => 'Winning option must belong to this city event.']);
        }

        try {
            DB::transaction(function () use ($cityEvent, $winningOption) {
                /** @var CityEvent $lockedCityEvent */
                $lockedCityEvent = CityEvent::query()->whereKey($cityEvent->id)->lockForUpdate()->firstOrFail();

                if ($lockedCityEvent->isSettled()) {
                    throw new RuntimeException('This city event is already settled.');
                }

                $pendingBets = Bet::query()
                    ->with('user.wallet')
                    ->where('city_event_id', $lockedCityEvent->id)
                    ->where('status', BetStatus::Pending)
                    ->lockForUpdate()
                    ->get();

                foreach ($pendingBets as $bet) {
                    if ($bet->betting_option_id === $winningOption->id) {
                        $bet->update([
                            'status' => BetStatus::Won,
                            'settled_at' => now(),
                        ]);

                        $wallet = $bet->user->ensureWallet();
                        $wallet->credit(
                            number_format((float) $bet->potential_payout, 2, '.', ''),
                            WalletTransactionType::BetWin,
                            $bet,
                            'Winning payout',
                        );
                    } else {
                        $bet->update([
                            'status' => BetStatus::Lost,
                            'settled_at' => now(),
                        ]);
                    }
                }

                $lockedCityEvent->update([
                    'winning_betting_option_id' => $winningOption->id,
                    'settled_at' => now(),
                ]);
            });
        } catch (RuntimeException $exception) {
            return back()->withErrors(['winning_betting_option_id' => $exception->getMessage()]);
        }

        return redirect()
            ->route('admin.events.cities.edit', $cityEvent->event_id)
            ->with('success', 'City event settled successfully.');
    }
}
