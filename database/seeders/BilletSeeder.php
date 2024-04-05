<?php

namespace Database\Seeders;

use App\Models\Billet;
use App\Models\Prix;
use App\Models\Reservation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BilletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $billets = Billet::factory(10)->make();
        $reservation_ids = Reservation::all()->pluck('id')->toArray();
        $prix_ids = Prix::all()->pluck('id')->toArray();
        foreach ($billets as $billet) {
            $billet->reservation_id = $reservation_ids[array_rand($reservation_ids)];
            $billet->prix_id = $prix_ids[array_rand($prix_ids)];
            $billet->save();
        }
    }
}
