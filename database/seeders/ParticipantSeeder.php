<?php

namespace Database\Seeders;

use App\Models\Artiste;
use App\Models\Evenement;
use Illuminate\Database\Seeder;

class ParticipantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $evenements = Evenement::all();
        $artistes = Artiste::all();

        foreach ($evenements as $evenement) {
            $numParticipants = mt_rand(1, 5);

            $selectedArtistes = $artistes->random($numParticipants);

            $ordre = 1;
            foreach ($selectedArtistes as $artiste) {
                $evenement->artistes()->attach($artiste, ['ordre' => $ordre]);
                $ordre++;
            }
        }
    }
}
