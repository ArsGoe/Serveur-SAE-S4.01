<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "nom" => fake()->lastName(),
            "prenom" => fake()->firstName(),
            "avatar" => "avatarsDefault.png",
            "adresse" => fake()->streetAddress(),
            "code_postal" => fake()->postcode(),
            "ville" => fake()->city(),
        ];
    }
}
