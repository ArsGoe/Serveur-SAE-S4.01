<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReservationResource;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function reservationsClient(Request $request)
    {
        $id_client = $request->user()->client->id;
        $reservations = Reservation::where('client_id', $id_client)->get();

        $reservations->map(function ($reservation) {
            $reservation->load('billets.prix');
            $reservation->billets->map(function ($billet) {
                $billet->prix_unitaire = $billet->prix->valeur;
            });
        });

        return ReservationResource::collection($reservations);
    }

    public function reservationsEvent(Request $request, $evenement_id)
    {
        $dateDebut = $request->query('date_debut');
        $dateFin = $request->query('date_fin');
        $id_client = $request->query('id_client');

        $query = Reservation::where('evenement_id', $evenement_id);

        if ($dateDebut && $dateFin) {
            $query->whereBetween('date_res', [$dateDebut, $dateFin]);
        }
        if ($id_client) {
            $query->where('client_id', $id_client);
        }

        $reservations = $query->get();

        $reservations->load('billets.prix');
        $reservations->map(function ($reservation) {
            $reservation->billets->map(function ($billet) {
                $billet->prix_unitaire = $billet->prix->valeur;
            });
        });

        return ReservationResource::collection($reservations);
    }

}
