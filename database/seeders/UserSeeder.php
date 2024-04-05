<?php

namespace Database\Seeders;

use App\Models\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory([
            'name' => 'Benoit Prigent',
            'email' => 'benoit.prigent@domain.fr',
            'password' => Hash::make('LaNoixDeCocoEstUnFruit'),
            'role' => UserRole::ADMIN
        ]) ->create();
        User::factory(9)->create();
    }
}
