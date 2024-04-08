<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Evenement;
use App\Models\Reservation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reservations = Reservation::factory(10)->make();
        $event_ids = Evenement::all()->pluck('id')->toArray();
        $client_ids = Client::all()->pluck('id')->toArray();
        foreach($reservations as $reservation) {
            $reservation->evenement_id = $event_ids[array_rand($event_ids)];
            $reservation->client_id = $client_ids[array_rand($client_ids)];
            $reservation->montant = 0;
            $reservation->nb_billets = 0;
            $reservation->save();
        }
    }
}
