<?php

namespace Database\Seeders;

use App\Enums\EventStatus;
use App\Models\BettingOption;
use App\Models\Bout;
use App\Models\City;
use App\Models\CityEvent;
use App\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EtfcSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ── Clear old data ─────────────────────────────────────────────────────
        DB::table('bets')->delete();
        DB::table('city_events')->delete();
        DB::table('events')->delete();
        DB::table('bouts')->delete();
        DB::table('fighters')->delete();

        $now = now();

        // ── Fighters ──────────────────────────────────────────────────────────
        DB::table('fighters')->insert([
            ['id' => 1, 'slug' => 'abrhamalem', 'name' => 'Abrhamalem', 'nickname' => null, 'weight_class' => null, 'sport' => 'Boxing', 'image_path' => 'fighters/Abrhamalem.png', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'slug' => 'tyson', 'name' => 'Tyson', 'nickname' => null, 'weight_class' => null, 'sport' => 'Boxing', 'image_path' => 'fighters/Tyson.png', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'slug' => 'sedo', 'name' => 'Sedo', 'nickname' => null, 'weight_class' => null, 'sport' => 'MMA', 'image_path' => 'fighters/Sedo.jpg', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'slug' => 'johnny', 'name' => 'Johnny', 'nickname' => null, 'weight_class' => null, 'sport' => 'MMA', 'image_path' => 'fighters/Johnny.jpg', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'slug' => 'rebik-sani', 'name' => 'Rebik Sani', 'nickname' => null, 'weight_class' => null, 'sport' => 'Muay Thai', 'image_path' => 'fighters/Rebik_Sani.png', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'slug' => 'sky-okony', 'name' => 'Sky Okony', 'nickname' => null, 'weight_class' => null, 'sport' => 'Muay Thai', 'image_path' => 'fighters/Sky_Okony.png', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'slug' => 'surafel-cheri', 'name' => 'Surafel Cheri', 'nickname' => null, 'weight_class' => null, 'sport' => 'Boxing', 'image_path' => 'fighters/Surafel_Cheri.png', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 8, 'slug' => 'desalegn', 'name' => 'Desalegn', 'nickname' => null, 'weight_class' => null, 'sport' => 'Boxing', 'image_path' => 'fighters/Desalegn.png', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 9, 'slug' => 'boyka', 'name' => 'Boyka', 'nickname' => null, 'weight_class' => null, 'sport' => 'MMA', 'image_path' => 'fighters/Boyka.png', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 10, 'slug' => 'endris', 'name' => 'Endris', 'nickname' => null, 'weight_class' => null, 'sport' => 'MMA', 'image_path' => 'fighters/Endris.png', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 11, 'slug' => 'frezer', 'name' => 'Frezer', 'nickname' => null, 'weight_class' => null, 'sport' => 'Muay Thai', 'image_path' => 'fighters/Frezer.png', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 12, 'slug' => 'habtamu', 'name' => 'Habtamu', 'nickname' => null, 'weight_class' => null, 'sport' => 'Muay Thai', 'image_path' => 'fighters/Habtamu.png', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 13, 'slug' => 'esubalew', 'name' => 'Esubalew', 'nickname' => null, 'weight_class' => null, 'sport' => 'Boxing', 'image_path' => 'fighters/Esubalew.png', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 14, 'slug' => 'biniyam', 'name' => 'Biniyam', 'nickname' => null, 'weight_class' => null, 'sport' => 'Boxing', 'image_path' => 'fighters/Biniyam.png', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 15, 'slug' => 'nikatehkina', 'name' => 'Nikatehkina', 'nickname' => null, 'weight_class' => null, 'sport' => 'MMA', 'image_path' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 16, 'slug' => 'robel', 'name' => 'Robel', 'nickname' => null, 'weight_class' => null, 'sport' => 'MMA', 'image_path' => 'fighters/Robel.png', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 17, 'slug' => 'zahara', 'name' => 'Zahara', 'nickname' => null, 'weight_class' => null, 'sport' => 'Muay Thai', 'image_path' => 'fighters/Zahara.png', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 18, 'slug' => 'yabsira', 'name' => 'Yabsira', 'nickname' => null, 'weight_class' => null, 'sport' => 'Muay Thai', 'image_path' => 'fighters/Yabsira.png', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 19, 'slug' => 'abenezer', 'name' => 'Abenezer', 'nickname' => null, 'weight_class' => null, 'sport' => 'Boxing', 'image_path' => 'fighters/Abenezer.png', 'created_at' => $now, 'updated_at' => $now],
            ['id' => 20, 'slug' => 'mesfin-biru', 'name' => 'Mesfin Biru', 'nickname' => null, 'weight_class' => null, 'sport' => 'Boxing', 'image_path' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 21, 'slug' => 'titan', 'name' => 'Titan', 'nickname' => null, 'weight_class' => null, 'sport' => 'MMA', 'image_path' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 22, 'slug' => 'coach-kal', 'name' => 'Coach Kal', 'nickname' => null, 'weight_class' => null, 'sport' => 'MMA', 'image_path' => 'fighters/Coach_Kal.png', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // ── Bouts ─────────────────────────────────────────────────────────────
        DB::table('bouts')->insert([
            ['id' => 1, 'sport' => 'Boxing', 'bout_number' => 1, 'is_main_event' => false, 'rounds' => 6, 'weight_class' => null, 'red_fighter_id' => 1, 'blue_fighter_id' => 2, 'event_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 2, 'sport' => 'MMA', 'bout_number' => 1, 'is_main_event' => true, 'rounds' => 5, 'weight_class' => null, 'red_fighter_id' => 3, 'blue_fighter_id' => 4, 'event_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 3, 'sport' => 'Muay Thai', 'bout_number' => 1, 'is_main_event' => false, 'rounds' => 5, 'weight_class' => null, 'red_fighter_id' => 5, 'blue_fighter_id' => 6, 'event_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 4, 'sport' => 'Boxing', 'bout_number' => 2, 'is_main_event' => false, 'rounds' => 6, 'weight_class' => null, 'red_fighter_id' => 7, 'blue_fighter_id' => 8, 'event_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 5, 'sport' => 'MMA', 'bout_number' => 2, 'is_main_event' => false, 'rounds' => 3, 'weight_class' => null, 'red_fighter_id' => 9, 'blue_fighter_id' => 10, 'event_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 6, 'sport' => 'Muay Thai', 'bout_number' => 2, 'is_main_event' => false, 'rounds' => 5, 'weight_class' => null, 'red_fighter_id' => 11, 'blue_fighter_id' => 12, 'event_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 7, 'sport' => 'Boxing', 'bout_number' => 3, 'is_main_event' => false, 'rounds' => 6, 'weight_class' => null, 'red_fighter_id' => 13, 'blue_fighter_id' => 14, 'event_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 8, 'sport' => 'MMA', 'bout_number' => 3, 'is_main_event' => false, 'rounds' => 3, 'weight_class' => null, 'red_fighter_id' => 15, 'blue_fighter_id' => 16, 'event_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 9, 'sport' => 'Muay Thai', 'bout_number' => 3, 'is_main_event' => false, 'rounds' => 5, 'weight_class' => null, 'red_fighter_id' => 17, 'blue_fighter_id' => 18, 'event_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 10, 'sport' => 'Boxing', 'bout_number' => 4, 'is_main_event' => false, 'rounds' => 6, 'weight_class' => null, 'red_fighter_id' => 19, 'blue_fighter_id' => 20, 'event_id' => null, 'created_at' => $now, 'updated_at' => $now],
            ['id' => 11, 'sport' => 'MMA', 'bout_number' => 4, 'is_main_event' => false, 'rounds' => 3, 'weight_class' => null, 'red_fighter_id' => 21, 'blue_fighter_id' => 22, 'event_id' => null, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // ── Events, CityEvents, BettingOptions ────────────────────────────────
        $cities = City::query()->where('active', true)->get();

        $bouts = Bout::query()->with(['redFighter', 'blueFighter'])->get();

        $fightDate = '2026-09-05';

        foreach ($bouts as $bout) {
            $red = $bout->redFighter;
            $blue = $bout->blueFighter;

            $title = $red->name.' vs '.$blue->name;

            if ($bout->is_main_event) {
                $description = $bout->sport.' · Main Event';
                $startsAt = $fightDate.' 20:00:00';
            } else {
                $description = $bout->sport.' · Bout '.$bout->bout_number;
                $startsAt = $fightDate.' '.date('H:i:s', strtotime('18:00:00 +'.$bout->bout_number * 10 .' minutes'));
            }

            $participants = [
                ['name' => $red->name, 'image' => $red->image_path],
                ['name' => $blue->name, 'image' => $blue->image_path],
            ];

            $event = Event::query()->create([
                'title' => $title,
                'description' => $description,
                'participants' => $participants,
                'starts_at' => $startsAt,
                'status' => EventStatus::Open,
                'bout_id' => $bout->id,
            ]);

            // Update the bout's event_id
            DB::table('bouts')->where('id', $bout->id)->update(['event_id' => $event->id]);

            foreach ($cities as $city) {
                $cityEvent = CityEvent::query()->create([
                    'city_id' => $city->id,
                    'event_id' => $event->id,
                    'active' => true,
                ]);

                $redOdds = rand(150, 280) / 100;
                $blueOdds = $redOdds + rand(10, 50) / 100;

                BettingOption::query()->create([
                    'city_event_id' => $cityEvent->id,
                    'name' => $red->name.' Wins',
                    'odds' => $redOdds,
                    'position' => 1,
                    'active' => true,
                ]);

                BettingOption::query()->create([
                    'city_event_id' => $cityEvent->id,
                    'name' => $blue->name.' Wins',
                    'odds' => $blueOdds,
                    'position' => 2,
                    'active' => true,
                ]);

                // Draw only for Boxing and Muay Thai
                if (in_array($bout->sport, ['Boxing', 'Muay Thai'], true)) {
                    $drawOdds = rand(300, 600) / 100;

                    BettingOption::query()->create([
                        'city_event_id' => $cityEvent->id,
                        'name' => 'Draw',
                        'odds' => $drawOdds,
                        'position' => 3,
                        'active' => true,
                    ]);
                }
            }
        }
    }
}
