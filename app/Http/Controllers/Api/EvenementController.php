<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EvenementResource;
use App\Models\Enums\Statut;
use App\Models\Enums\UserRole;
use App\Models\Evenement;
use App\Models\Lieu;
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

    public static function cat_dispo($evenement): array
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

    public function store(Request $request) : JsonResponse
    {
        $user = $request->user();
        if ($user->role != UserRole::GESTIONNAIRE && $user->role != UserRole::ADMIN) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'titre' => 'required',
            'type' => 'required',
            'description' => 'required',
            'date_event' => 'required',
            'lieu_id' => 'required|exists:lieux,id',
            'artistes' => 'required|array',
            'artistes.*.id' => 'required|exists:artistes,id',
            'artistes.*.ordre' => 'required|integer',
            'prix' => 'required|array',
            'prix.*.categorie' => 'required',
            'prix.*.nombre' => 'required|integer',
            'prix.*.valeur' => 'required|numeric',
        ]);
        $evenement = new Evenement();
        $evenement->titre = $request->titre;
        $evenement->type = $request->type;
        $evenement->description = $request->description;
        $evenement->date_event = $request->date_event;
        $evenement->lieu_id = $request->lieu_id;
        $evenement->save();

        foreach ($request->artistes as $artiste) {
            $evenement->artistes()->syncWithoutDetaching([$artiste['id'] => ['ordre' => $artiste['ordre']]]);
        }

        $evenement->prix()->createMany($request->prix);

        return response()->json(['message' => 'Evenement created'], 201);
    }


    public function lieux(Request $request) : JsonResponse
    {
        $user = $request->user();
        if ($user->role == UserRole::NON_ACTIF) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $lieux = Lieu::all();

        return response()->json($lieux);
    }

    public function prix(Request $request, int $id) : JsonResponse
    {
        $user = $request->user();
        if ($user->role == UserRole::NON_ACTIF) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $evenement = Evenement::find($id);
        if (!$evenement) {
            return response()->json(['message' => 'Evenement not found'], 404);
        }

        return response()->json($this->cat_dispo($evenement));
    }

    public function update(Request $request, int $id) : JsonResponse
    {
        $user = $request->user();
        if ($user->role != UserRole::GESTIONNAIRE && $user->role != UserRole::ADMIN) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $evenement = Evenement::find($id);
        if (!$evenement) {
            return response()->json(['message' => 'Evenement not found'], 404);
        }

        $validatedData = $request->validate([
            'titre' => 'sometimes|required',
            'type' => 'sometimes|required',
            'description' => 'sometimes|required',
            'date_event' => 'sometimes|required',
            'lieu_id' => 'sometimes|required|exists:lieux,id',
        ]);

        $evenement->fill($validatedData);
        $evenement->save();

        return response()->json(['message' => 'Evenement updated']);
    }

    public function updatePrix(Request $request, int $id) : JsonResponse
    {
        $user = $request->user();
        if ($user->role != UserRole::GESTIONNAIRE && $user->role != UserRole::ADMIN) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $evenement = Evenement::find($id);
        if (!$evenement) {
            return response()->json(['message' => 'Evenement not found'], 404);
        }

        $request->validate([
            'prix' => 'required|array',
            'prix.*.categorie' => 'required',
            'prix.*.nombre' => 'required|integer',
            'prix.*.valeur' => 'required|numeric',
        ]);

        $evenement->prix()->delete();
        $evenement->prix()->createMany($request->prix);

        return response()->json(['message' => 'Prix updated']);
    }

    public function updateArtistes(Request $request, int $id) : JsonResponse
    {
        $user = $request->user();
        if ($user->role != UserRole::GESTIONNAIRE && $user->role != UserRole::ADMIN) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $evenement = Evenement::find($id);
        if (!$evenement) {
            return response()->json(['message' => 'Evenement not found'], 404);
        }

        $request->validate([
            'artistes' => 'required|array',
            'artistes.*.id' => 'required|exists:artistes,id',
            'artistes.*.ordre' => 'required|integer',
        ]);

        foreach ($request->artistes as $artiste) {
            $evenement->artistes()->syncWithoutDetaching([$artiste['id'] => ['ordre' => $artiste['ordre']]]);
        }

        return response()->json(['message' => 'Artistes updated']);
    }


    public function destroy(Request $request, int $id) : JsonResponse
    {
        $user = $request->user();
        if ($user->role != UserRole::ADMIN) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $evenement = Evenement::find($id);
        if (!$evenement) {
            return response()->json(['message' => 'Evenement not found'], 404);
        }

        if ($evenement->reservations->count() > 0) {
            return response()->json(['message' => 'Evenement has reservations'], 400);
        }

        $evenement->delete();

        return response()->json(['message' => 'Evenement deleted']);
    }
}
