<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use App\Models\User;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::all();
        return ClientResource::collection($clients);
    }

    public function store(ClientRequest $request)
    {
        $user = new User();
        $user->name = $request->nom;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->save();
        $client = new Client();
        $client->nom = $request->nom;
        $client->prenom = $request->prenom;
        $client->adresse = $request->adresse;
        $client->code_postal = $request->code_postal;
        $client->ville = $request->ville;
        $client->avatar = "https://www.intima-et-moi.fr/mon-hygiene-intime/quel-est-le-ph-de-la-zone-intime/";
        $client->user_id = User::findOrFail(User::findbyemail($user->email))->id;
        $client->save();
        return response()->json([
            'status' => true,
            'message' => "Client créé avec succès",
            'client' => $client
        ]);
    }

    public function show(string $id)
    {
        $personne = Client::findOrFail($id);
        return new ClientResource($personne);
    }


    public function update(ClientRequest $request, string $id)
    {
        $client = Client::findOrFail($id);
        $client->nom = $request->nom;
        $client->prenom = $request->prenom;
        $client->adresse = $request->adresse;
        $client->code_postal = $request->code_postal;
        $client->ville = $request->ville;
        $client->save();
        return response()->json([
            'status' => true,
            'message' => "Client modifié avec succès",
            'client' => $client
        ]);
    }

    public function destroy(string $id)
    {
        $client = Client::findOrFail($id);
        $client->delete();
        return response()->json([
            'status' => true,
            'message' => "Client supprimé"
        ], 200);
    }

    public function trimNom(string $nom) {
        return response()->json([
            'status' => true,
            'message' => Client::all()->where('nom', $nom)
        ]);
    }

    public function triNom(string $nom) {
        $nom = array_column((array)Client::all(), 'nom');
        return response()->json([
            'status' => true,
            'message' => array_multisort($nom, SORT_ASC, Client::all())
        ]);
    }

    public function triVille(string $nom) {
        $ville = array_column((array)Client::all(), 'ville');
        return response()->json([
            'status' => true,
            'message' => array_multisort($ville, SORT_ASC, Client::all())
        ]);
    }
}
