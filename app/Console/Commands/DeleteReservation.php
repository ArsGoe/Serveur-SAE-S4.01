<?php

namespace App\Console\Commands;

use App\Models\Enums\Statut;
use App\Models\Reservation;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Symfony\Component\Console\Command\Command as CommandAlias;

class DeleteReservation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-reservation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'delete reservation '.Statut::EN_ATTENTE;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $reservations = Reservation::where('statut', Statut::EN_ATTENTE)->get();

        $reservations->each(function($reservation) {
            if (now()->diffInHours($reservation->date_res) >= 12) {
                $reservation->delete();
            }
        });

        return CommandAlias::SUCCESS;
    }
}
