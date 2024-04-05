<?php

namespace Database\Factories;

use App\Models\Artiste;
use App\Models\Enums\Genre;
use App\Models\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Artiste>
 */
class ArtisteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => $this->faker->lastName(),
            'genre' => $this->faker->randomElement(Genre::getValues())
        ];
    }
}
