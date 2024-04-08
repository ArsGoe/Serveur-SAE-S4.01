<?php

namespace Database\Seeders;

use App\Models\Evenement;
use App\Models\Lieu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EvenementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $events = Evenement::factory(10)->make();
        $lieu_ids = Lieu::all()->pluck('id')->toArray();
        foreach ($events as $event) {
            $event->lieu_id = $lieu_ids[array_rand($lieu_ids)];
            $event->save();
        }
    }
}
