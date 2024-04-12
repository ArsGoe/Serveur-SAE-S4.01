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
use OpenApi\Attributes as OA;

class EvenementController extends Controller
{

    #[OA\Get(
        path: "/evenements",
        operationId: "index-evenements",
        description: "Liste des evenements",
        tags: ["Evenements"],
        responses: [
            new OA\Response(response: 200,
                description: "Liste des evenements",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "evenements", ref: "#/components/schemas/Evenement", type: "object"),
                ], type: "object"))
        ]
    )]
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

    #[OA\Get(
        path: "/evenements/{id}",
        operationId: "show-evenement",
        description: "Affiche un evenement",
        tags: ["Evenements"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "Identifiant de l'evenement",
                in: "path", required: true,
                schema: new OA\Schema(type: "integer", format: 'int64'))
        ],
        responses: [
            new OA\Response(response: 200,
                description: "Affiche un evenement",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "evenement", ref: "#/components/schemas/Evenement", type: "object"),
                    new OA\Property(property: "cat_dispo", type: "array",
                        items: new OA\Schema(ref: "#/components/schemas/cat_dispo"))],
                    type: "object")),
            new OA\Response(response: 401, description: "Non autorisé",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "message", type: "string"),
                ], type: "object")),
            new OA\Response(response: 404, description: "Evenement non trouvé",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "message", type: "string"),
                ], type: "object"))
        ]
    )]
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

    #[OA\Schema(
        schema: "cat_dispo",
        properties: [
            new OA\Property(property: "categorie", type: "string"),
            new OA\Property(property: "prix", type: "number"),
            new OA\Property(property: "nombre_places", type: "integer"),
            new OA\Property(property: "nb_places_dispo", type: "integer"),
        ],
        type: "object"
    )]
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

    #[OA\Post(
        path: "/evenements",
        operationId: "store-evenement",
        description: "Crée un evenement",
        tags: ["Evenements"],
        responses: [
            new OA\Response(response: 201,
                description: "Evenement créé",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "message", type: "string"),
                ], type: "object")),
            new OA\Response(response: 401, description: "Non autorisé",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "message", type: "string"),
                ], type: "object"))
        ]
    )]
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

    #[OA\Get(
        path: "/lieux",
        operationId: "lieux",
        description: "list of lieux",
        tags: ["Evenements"],
        responses: [
            new OA\Response(response: 200,
                description: "Liste des lieux",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "lieux", ref: "#/components/schemas/Lieu", type: "object"),
                ], type: "object")),
            new OA\Response(response: 401,
                description: "Unauthorized",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "message", type: "string"),
                ], type: "object)"))
        ]
    )]
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

    #[OA\Put(
        path: "/evenements/{id}",
        operationId: "update-evenement",
        description: "Met à jour un evenement",
        tags: ["Evenements"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "Identifiant de l'evenement",
                in: "path", required: true,
                schema: new OA\Schema(type: "integer", format: 'int64'))
        ],
        responses: [
            new OA\Response(response: 200,
                description: "Evenement mis à jour",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "message", type: "string"),
                ], type: "object")),
            new OA\Response(response: 401, description: "Non autorisé",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "message", type: "string"),
                ], type: "object")),
            new OA\Response(response: 404, description: "Evenement non trouvé",
                content: new OA\JsonContent(properties: [
                    new OA\Property(property: "message", type: "string"),
                ], type: "object"))
        ]
    )]
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
