<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EvenementResource;
use App\Models\Enums\Statut;
use App\Models\Enums\UserRole;
use App\Models\Evenement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EvenementController extends Controller
{
    public function index(Request $request) : JsonResponse
    {
        $evenements = Evenement::all();

        $user = $request->user();
        if ($user && ($user->role == UserRole::ACTIF || $user->role == UserRole::NON_ACTIF)) {
            $evenements = $evenements->sortBy('date_event');
            $evenements = $evenements->filter(function ($evenement) {
                return $evenement->date_event >= now();
            });
        } else {
            $evenements = $evenements->sortBy('date_event', descending: true);
        }

        if ($request->has('type')) {
            $evenements = $evenements->where('type', $request->type);
        }

        if ($request->has('lieu')) {
            $evenements = $evenements->where('lieu_id', $request->lieu);
        }

       return response()->json(
                EvenementResource::collection($evenements)
       );
    }

    public function show(Request $request, int $id) : JsonResponse
    {
        $user = $request->user();
        if ($user->role == UserRole::NON_ACTIF) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $evenement = Evenement::with('prix')->find($id);
        if (!$evenement) {
            return response()->json(['message' => 'Evenement not found'], 404);
        }

        return response()->json(
            [new EvenementResource($evenement),
                'cat_dispo' => $this->cat_dispo($evenement)]
        );
    }

    public function cat_dispo($evenement): array
    {
        $cats = [];

        foreach ($evenement->prix as $cat) {
            $nb_places = $cat->nombre;

            $nb_reservations = $cat->billets->where('reservation.statut', '!=', Statut::ANNULE)->count();

            $nb_places_dispo = $nb_places - $nb_reservations;

            if ($nb_places_dispo <= 0) {
                continue;
            }
            $cats[] = [
                'categorie' => $cat->categorie,
                'prix' => $cat->valeur,
                'nombre_places' => $nb_places,
                'nb_places_dispo' => $nb_places_dispo,
            ];
        }

        return $cats;
    }

}
