<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReservationResource;
use App\Jobs\SendMailPaiement;
use App\Models\Billet;
use App\Models\Client;
use App\Models\Enums\Statut;
use App\Models\Enums\UserRole;
use App\Models\Evenement;
use App\Models\Prix;
use App\Models\Reservation;
use Database\Seeders\BilletSeeder;
use Illuminate\Http\JsonResponse;
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

    public function statsEvenement(Request $request, int $idEvent): JsonResponse
    {
        $user = $request->user();
        if ($user->role != UserRole::GESTIONNAIRE && $user->role != UserRole::ADMIN) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $evenement = Evenement::find($idEvent);
        if (!$evenement) {
            return response()->json(['message' => 'Evenement not found'], 404);
        }

        $cats = EvenementController::cat_dispo($evenement);

        $nbPlacesPaye = self::nbPlacesPaye($evenement);
        $nbPlaces = self::nbPlaces($evenement);
        $nbPlacesEdite = self::nbPlacesEdite($evenement);

        if ($nbPlaces == 0) {
            $nbPlaces = 1;
        }
        $nb_clients = $evenement->reservations->pluck('client_id')->unique()->count();
        return response()->json(
            ["reservations" => $cats,
                "pourcentage_paye" => ($nbPlacesPaye / $nbPlaces)*100,
                "pourcentage_edit" => ($nbPlacesEdite / $nbPlaces)*100,
                "nb_cients" => $nb_clients]
        );
    }

    public static function nbPlaces(Evenement $evenement)
    {
        $reservations =  $evenement->reservations;
        $nbPlaces = 0;
        foreach ($reservations as $reservation) {
            $billets = $reservation->billets;
            foreach ($billets as $billet) {
                $nbPlaces += $billet->quantite;
            }
        }
        return $nbPlaces;
    }

    public static function nbPlacesPaye(Evenement $evenement)
    {
        $reservationsPaye = $evenement->reservations->where("statut", Statut::PAYE);
        $nbPlacesPaye = 0;
        foreach ($reservationsPaye as $reservation) {
            $billets = $reservation->billets;
            foreach ($billets as $billet) {
                $nbPlacesPaye += $billet->quantite;
            }
        }
        return $nbPlacesPaye;
    }

    public static function nbPlacesEdite(Evenement $evenement)
    {
        $reservationsEdite = $evenement->reservations->where("statut", Statut::BILLET_EDITE);
        $nbPlacesEdite = 0;
        foreach ($reservationsEdite as $reservation) {
            $billets = $reservation->billets;
            foreach ($billets as $billet) {
                $nbPlacesEdite += $billet->quantite;
            }
        }
        return $nbPlacesEdite;
    }

    public function destroy(Request $request, int $id)
    {
        $reservation = Reservation::find($id);
        if (!$reservation) {
            return response()->json(['message' => 'Reservation not found'], 404);
        }
        if ($reservation->statut !== Statut::EN_ATTENTE || now()->diffInHours($reservation->date_res) < 12) {
            return response()->json(['message' => 'You can\'t delete this reservation'], 400);
        }
        $reservation->delete();

        return response()->json(['message' => 'Reservation deleted']);
    }

    public function paiement(int $id)
    {
        $reservation = Reservation::find($id);
        if (!$reservation) {
            return response()->json(['message' => 'Reservation not found'], 404);
        }
        if ($reservation->statut !== Statut::EN_ATTENTE) {
            return response()->json(['message' => 'You can\'t pay for this reservation'], 400);
        }
        $client = Client::find($reservation->client_id);
        SendMailPaiement::dispatch($client);

        $reservation->statut = Statut::PAYE;
        $reservation->save();



        return response()->json(['message' => 'Reservation paid']);
    }
}
