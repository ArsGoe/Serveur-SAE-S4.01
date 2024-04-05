<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(ClientSeeder::class);
        $this->call(LieuSeeder::class);
        $this->call(ArtisteSeeder::class);
        $this->call(EvenementSeeder::class);
        $this->call(PrixSeeder::class);
        $this->call(ReservationSeeder::class);
    }
}
