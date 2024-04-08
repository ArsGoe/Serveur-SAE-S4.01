<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Enums\Statut;
use App\Models\Evenement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date_res' => $this->faker->dateTime(),
            'nb_billets' => $this->faker->numberBetween(1, 10),
            'montant' => $this->faker->randomFloat(2, 4.99, 9999.99), // doit être calculé avec prix et billets
            'statut' => $this->faker->randomElement(Statut::getValues()),
        ];
    }
}
