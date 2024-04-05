<?php

namespace Database\Seeders;

use App\Models\Artiste;
use App\Models\Enums\Genre;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArtisteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Artiste::factory([
            'nom' => 'Baki',
            'genre' => Genre::SPORTIF
        ])->create();
        Artiste::factory(9)->create();
    }
}
