<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lieu>
 */
class LieuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => $this->faker->name,
            'adresse' => $this->faker->address,
            'code_postal' => $this->faker->postcode,
            'ville' => $this->faker->city,
            'lat' => $this->faker->latitude,
            'long' => $this->faker->longitude,
        ];
    }
}
