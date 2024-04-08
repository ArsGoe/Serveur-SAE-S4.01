<?php

namespace Database\Factories;

use App\Models\Prix;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prix>
 */
class PrixFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'categorie' => $this->faker->word,
            'nombre' => $this->faker->randomDigit(),
            'valeur' => $this->faker->randomFloat(2, 0, 1000),
        ];
    }
}
