<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReservationResource;
use App\Models\Billet;
use App\Models\Enums\Statut;
use App\Models\Enums\UserRole;
use App\Models\Evenement;
use App\Models\Prix;
use App\Models\Reservation;
use Database\Seeders\BilletSeeder;
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

    public function store(Request $request)
    {
        $request->validate([
            'reservations' => 'required|array',
            'reservations.*.categorie' => 'required|exists:prix,categorie',
            'reservations.*.nombre_places' => 'required|integer|min:1',
        ]);

        $reservation = new Reservation();
        $reservation->date_res = now();
        $reservation->statut = Statut::EN_ATTENTE;

        $role = $request->user()->role;
        if ($role === UserRole::ACTIF) {
            $reservation->client_id = $request->user()->client->id;
        } else {
            $request->validate([
                'client_id' => 'required|exists:clients,id',
            ]);
            $reservation->client_id = $request->client_id;
        }

        $evenement_ids = [];
        $billets = [];

        foreach ($request->reservations as $reservationData) {
            $categorie = $reservationData['categorie'];
            $nombrePlaces = $reservationData['nombre_places'];

            $evenement = Evenement::whereHas('prix', function ($query) use ($categorie) {
                $query->where('categorie', $categorie);
            })->first();
            $evenement_ids[] = $evenement->id;

            if ($evenement && $this->assezDePlace($evenement, $nombrePlaces)) {
                $billets[] = [
                    'evenement_id' => $evenement->id,
                    'categorie' => $categorie,
                    'nombre_places' => $nombrePlaces,
                ];
            } else {
                return response()->json(['message' => 'Places insuffisantes pour la catégorie ' . $categorie], 400);
            }
        }
        if ($this->tousIdentiques($evenement_ids)) {
            $reservation->evenement_id = $evenement_ids[0];
            $reservation->save();
            $this->creerBillets($billets, $reservation);
            BilletSeeder::calculMontant($reservation);
            return response()->json(['message' => 'Réservation créée avec succès'], 201);
        } else {
            return response()->json(['message' => 'Impossible de réserver pour plusieurs événements en même temps'], 400);
        }
    }

    private function assezDePlace($evenement, $nombrePlaces)
    {
        $catsDispo = EvenementController::cat_dispo($evenement);

        foreach ($catsDispo as $catDispo) {
            if ($catDispo['nombre_places'] >= $nombrePlaces) {
                return true;
            }
        }
        return false;
    }

    public static function tousIdentiques($array): bool
    {
        if (count($array) <= 1) {
            return true;
        }
        $firstElement = $array[0];
        foreach ($array as $element) {
            if ($element !== $firstElement) {
                return false;
            }
        }
        return true;
    }

    private function creerBillets($billets, $reservation)
    {
        foreach ($billets as $billetData) {
            $billet = new Billet();
            $billet->quantite = $billetData['nombre_places'];
            $prix = Prix::where('categorie', $billetData['categorie'])
                ->where('evenement_id', $billetData['evenement_id'])
                ->first();
            $billet->prix_id = $prix->id;
            $billet->reservation_id = $reservation->id;
            $billet->save();
        }
    }
}
