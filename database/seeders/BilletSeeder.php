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
        $reservations = Reservation::all();
        foreach ($reservations as $reservation) {
            $prix_ids = Prix::all()->pluck('id')->toArray();
            $nb_billets = rand(1, 5);
            for ($i = 0; $i < $nb_billets; $i++) {
                $billet = Billet::factory()->make();
                $prix_id = $prix_ids[array_rand($prix_ids)];
                $billet->prix_id =  $prix_id;
                $prix_ids = array_diff($prix_ids, [$prix_id]);
                $billet->reservation_id = $reservation->id;
                $billet->save();
            }
        }

        // update montant reservation
        foreach ($reservations as $reservation) {
            $montant = 0;
            foreach ($reservation->billets as $billet) {
                $montant += $billet->prix->valeur * $billet->quantite;
            }
            $reservation->nb_billets = $reservation->billets->count();
            $reservation->montant = $montant;
            $reservation->update();
        }
    }
}
