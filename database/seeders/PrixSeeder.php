<?php

namespace Database\Seeders;

use App\Models\Evenement;
use App\Models\Prix;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PrixSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lesPrix = Prix::factory(10)->make();
        $evenement_ids = Evenement::all()->pluck('id')->toArray();
        foreach ($lesPrix as $prix) {
            $prix->evenement_id = $evenement_ids[array_rand($evenement_ids)];
            $prix->save();
        }
    }
}
