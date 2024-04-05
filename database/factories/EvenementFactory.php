<?php

namespace Database\Factories;

use App\Models\Enums\Type;
use App\Models\Evenement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Evenement>
 */
class EvenementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'titre' => $this->faker->title,
            'type' => $this->faker->randomElement(Type::getValues()),
            'description' => $this->faker->paragraph,
            'date_event' => $this->faker->dateTimeBetween('-3 months', '+3 months'),
        ];
    }
}
