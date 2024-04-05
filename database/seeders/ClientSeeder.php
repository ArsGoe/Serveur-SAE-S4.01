<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        foreach($users as $user) {
            $name = explode(' ', $user->name);
            Client::factory([
                'nom' => $name[0],
                'prenom' => $name[1],
                'user_id' => $user->id,
            ])->create();
        }
    }
}
