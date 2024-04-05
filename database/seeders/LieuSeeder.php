<?php

namespace Database\Seeders;

use App\Models\Lieu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LieuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Lieu::factory([
            'nom' => 'Accor Arena',
            'adresse' => '8 Bd de Bercy',
            'code_postal' => '75012',
            'ville' => 'Paris'
        ])->create();
        Lieu::factory(9)->create();
    }
}
