<?php

namespace Database\Seeders;

use App\Enums\WalletRequestStatus;
use App\Enums\WalletRequestType;
use App\Enums\WalletTransactionType;
use App\Models\City;
use App\Models\CityBanner;
use App\Models\User;
use App\Models\WalletRequest;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // ── Cities ────────────────────────────────────────────────────────────
        $addis = City::query()->updateOrCreate(
            ['slug' => 'addis'],
            [
                'name' => 'Addis Ababa',
                'hero_title' => 'Bet in Addis Ababa',
                'hero_subtitle' => 'City-specific odds for local events',
                'active' => true,
            ]
        );

        $gambella = City::query()->updateOrCreate(
            ['slug' => 'gambella'],
            [
                'name' => 'Gambella',
                'hero_title' => 'Bet in Gambella',
                'hero_subtitle' => 'Local odds for Gambella events',
                'active' => true,
            ]
        );

        $hawassa = City::query()->updateOrCreate(
            ['slug' => 'hawassa'],
            [
                'name' => 'Hawassa',
                'hero_title' => 'Hawassa Betting Hub',
                'hero_subtitle' => 'The best odds in southern Ethiopia',
                'active' => true,
            ]
        );

        $direDawa = City::query()->updateOrCreate(
            ['slug' => 'dire-dawa'],
            [
                'name' => 'Dire Dawa',
                'hero_title' => 'Bet in Dire Dawa',
                'hero_subtitle' => "Eastern Ethiopia's premier betting platform",
                'active' => true,
            ]
        );

        $cities = [$addis, $gambella, $hawassa, $direDawa];

        // ── City Banners ──────────────────────────────────────────────────────
        foreach ($cities as $city) {
            CityBanner::query()->updateOrCreate(
                ['city_id' => $city->id, 'title' => 'Welcome to '.$city->name],
                [
                    'subtitle' => 'Register now and get your first bet bonus',
                    'image' => null,
                    'link' => null,
                    'active' => true,
                    'position' => 1,
                ]
            );
            CityBanner::query()->updateOrCreate(
                ['city_id' => $city->id, 'title' => 'ETFC Fight Night'],
                [
                    'subtitle' => "Don't miss the live fight card — bet now",
                    'image' => null,
                    'link' => null,
                    'active' => true,
                    'position' => 2,
                ]
            );
        }

        // ── Admin ─────────────────────────────────────────────────────────────
        User::query()->updateOrCreate(
            ['phone' => env('ADMIN_PHONE', '0900000000')],
            [
                'name' => 'Admin',
                'password' => bcrypt(env('ADMIN_PASSWORD', 'password')),
                'is_admin' => true,
                'city_id' => null,
            ]
        );

        // ── Customers ─────────────────────────────────────────────────────────
        $customerDefs = [
            ['name' => 'Abel Tesfaye', 'phone' => '0911111111', 'city' => $addis, 'balance' => '2000.00'],
            ['name' => 'Meron Kebede', 'phone' => '0922222222', 'city' => $addis, 'balance' => '5000.00'],
            ['name' => 'Dawit Alemu', 'phone' => '0933333333', 'city' => $gambella, 'balance' => '1500.00'],
            ['name' => 'Tigist Haile', 'phone' => '0944444444', 'city' => $gambella, 'balance' => '3000.00'],
            ['name' => 'Yonas Bekele', 'phone' => '0955555555', 'city' => $hawassa, 'balance' => '2500.00'],
            ['name' => 'Selam Girma', 'phone' => '0966666666', 'city' => $hawassa, 'balance' => '4000.00'],
            ['name' => 'Biruk Solomon', 'phone' => '0977777777', 'city' => $direDawa, 'balance' => '1000.00'],
            ['name' => 'Hana Lemma', 'phone' => '0988888888', 'city' => $direDawa, 'balance' => '6000.00'],
            ['name' => 'Tesfaye Worku', 'phone' => '0999999991', 'city' => $addis, 'balance' => '800.00'],
            ['name' => 'Almaz Tadesse', 'phone' => '0999999992', 'city' => $hawassa, 'balance' => '3500.00'],
        ];

        foreach ($customerDefs as $def) {
            $user = User::query()->updateOrCreate(
                ['phone' => $def['phone']],
                [
                    'name' => $def['name'],
                    'password' => bcrypt(env('CUSTOMER_PASSWORD', 'password')),
                    'is_admin' => false,
                    'city_id' => $def['city']->id,
                ]
            );

            $wallet = $user->ensureWallet();

            if (bccomp((string) $wallet->balance, '0', 2) === 0) {
                $request = WalletRequest::query()->create([
                    'user_id' => $user->id,
                    'wallet_id' => $wallet->id,
                    'type' => WalletRequestType::Deposit,
                    'amount' => $def['balance'],
                    'status' => WalletRequestStatus::Approved,
                    'note' => 'Initial deposit',
                ]);

                $wallet->credit($def['balance'], WalletTransactionType::Deposit, $request, 'Initial deposit approved');
            }
        }
    }
}
