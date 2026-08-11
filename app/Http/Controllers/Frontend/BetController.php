<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\BetStatus;
use App\Enums\EventStatus;
use App\Enums\WalletTransactionType;
use App\Http\Controllers\Controller;
use App\Models\Bet;
use App\Models\BettingOption;
use App\Models\City;
use App\Models\CityEvent;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use RuntimeException;

class BetController extends Controller
{
    public function index(Request $request): View
    {
        $bets = $request->user()
            ->bets()
            ->with(['cityEvent.city', 'cityEvent.event', 'bettingOption'])
            ->latest()
            ->paginate(15);

        return view('frontend.bets.index', compact('bets'));
    }

    public function store(Request $request, City $city, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'betting_option_id' => ['required', 'integer', 'exists:betting_options,id'],
            'stake' => ['required', 'numeric', 'min:1', 'max:1000000'],
        ]);

        $user = $request->user();

        abort_unless($city->active, 404);
        abort_if($user->is_admin, 403);

        if ($user->city_id !== $city->id) {
            return back()->withErrors([
                'stake' => 'You can only place bets in your registered city.',
            ]);
        }

        $cityEvent = $city->cityEvents()
            ->where('event_id', $event->id)
            ->where('active', true)
            ->firstOrFail();

        if ($cityEvent->isSettled()) {
            return back()->withErrors(['stake' => 'This event has already been settled.']);
        }

        if ($event->status !== EventStatus::Open) {
            return back()->withErrors(['stake' => 'Betting is only open for open events.']);
        }

        if ($event->starts_at->isPast()) {
            return back()->withErrors(['stake' => 'This event has already started.']);
        }

        $option = BettingOption::query()
            ->whereKey($validated['betting_option_id'])
            ->where('city_event_id', $cityEvent->id)
            ->where('active', true)
            ->first();

        if (! $option) {
            return back()->withErrors(['betting_option_id' => 'Invalid betting option for this event.']);
        }

        $stake = number_format((float) $validated['stake'], 2, '.', '');
        $odds = number_format((float) $option->odds, 2, '.', '');
        $potentialPayout = bcmul($stake, $odds, 2);

        try {
            DB::transaction(function () use ($user, $cityEvent, $option, $stake, $odds, $potentialPayout) {
                $wallet = $user->ensureWallet();
                $wallet = $wallet->newQuery()->whereKey($wallet->id)->lockForUpdate()->firstOrFail();

                if (bccomp($wallet->availableBalance(), $stake, 2) === -1) {
                    throw new RuntimeException('Insufficient available balance.');
                }

                $bet = Bet::query()->create([
                    'user_id' => $user->id,
                    'city_event_id' => $cityEvent->id,
                    'betting_option_id' => $option->id,
                    'stake' => $stake,
                    'odds' => $odds,
                    'potential_payout' => $potentialPayout,
                    'status' => BetStatus::Pending,
                ]);

                $wallet->debit(
                    $stake,
                    WalletTransactionType::BetStake,
                    $bet,
                    'Bet stake on '.$option->name,
                );
            });
        } catch (RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors(['stake' => $exception->getMessage()]);
        }

        return redirect()
            ->route('bets.index')
            ->with('success', 'Bet placed successfully.');
    }

    public function storeMulti(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bets' => ['required', 'array', 'min:1', 'max:10'],
            'bets.*.city_event_id' => ['required', 'integer', 'exists:city_events,id'],
            'bets.*.betting_option_id' => ['required', 'integer', 'exists:betting_options,id'],
            'bets.*.stake' => ['required', 'numeric', 'min:1', 'max:1000000'],
        ]);

        $user = $request->user();
        abort_if($user->is_admin, 403);

        try {
            $placed = DB::transaction(function () use ($user, $validated) {
                $wallet = $user->ensureWallet();
                $wallet = $wallet->newQuery()->whereKey($wallet->id)->lockForUpdate()->firstOrFail();

                $totalStake = '0';
                $betsToCreate = [];

                foreach ($validated['bets'] as $betData) {
                    $cityEvent = CityEvent::query()
                        ->whereKey($betData['city_event_id'])
                        ->where('active', true)
                        ->where('city_id', $user->city_id)
                        ->with('event')
                        ->firstOrFail();

                    if ($cityEvent->isSettled()) {
                        throw new RuntimeException("'{$cityEvent->event->title}' has already been settled.");
                    }

                    $event = $cityEvent->event;

                    if ($event->status !== EventStatus::Open) {
                        throw new RuntimeException("'{$event->title}' is not open for betting.");
                    }

                    if ($event->starts_at->isPast()) {
                        throw new RuntimeException("'{$event->title}' has already started.");
                    }

                    $option = BettingOption::query()
                        ->whereKey($betData['betting_option_id'])
                        ->where('city_event_id', $cityEvent->id)
                        ->where('active', true)
                        ->firstOrFail();

                    $stake = number_format((float) $betData['stake'], 2, '.', '');
                    $odds = number_format((float) $option->odds, 2, '.', '');
                    $potentialPayout = bcmul($stake, $odds, 2);
                    $totalStake = bcadd($totalStake, $stake, 2);

                    $betsToCreate[] = compact('cityEvent', 'option', 'stake', 'odds', 'potentialPayout');
                }

                if (bccomp($wallet->availableBalance(), $totalStake, 2) === -1) {
                    throw new RuntimeException('Insufficient balance for the total stake of '.number_format((float) $totalStake, 2).' ETB.');
                }

                foreach ($betsToCreate as $betData) {
                    $bet = Bet::query()->create([
                        'user_id' => $user->id,
                        'city_event_id' => $betData['cityEvent']->id,
                        'betting_option_id' => $betData['option']->id,
                        'stake' => $betData['stake'],
                        'odds' => $betData['odds'],
                        'potential_payout' => $betData['potentialPayout'],
                        'status' => BetStatus::Pending,
                    ]);

                    $wallet->debit(
                        $betData['stake'],
                        WalletTransactionType::BetStake,
                        $bet,
                        'Bet stake on '.$betData['option']->name,
                    );
                }

                return count($betsToCreate);
            });

            $label = $placed.' bet'.($placed !== 1 ? 's' : '').' placed successfully.';

            return response()->json(['placed' => $placed, 'message' => $label]);
        } catch (RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
