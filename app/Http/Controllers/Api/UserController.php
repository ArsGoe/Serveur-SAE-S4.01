<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function destroy(string $id) {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json([
            'status' => true,
            'message' => "User Destroyed successfully!",
        ]);
    }
    public function profil(string $id) {
        $user = User::findOrFail($id);
        $client = Client::all()->where('user_id',$id)->first();
        return response()->json([
            'status' => true,
            'message' =>  [
                'nom'=> $client->nom,
                'prenom' => $client->prenom,
                'adresse' => $client->adresse,
                'code_postal' => $client->code_postal,
                'ville' => $client->ville,
                'name' => $user->name,
                'email' => $user->email,
                'role' =>$user->role
            ]
        ]);
    }

    public function update(UserRequest $request, string $id)
    {
        $user = User::findOrFail($id);
        $user->nom = $request->name;
        $user->prenom = $request->email;
        $user->adresse = $request->password;
        $user->save();
        return response()->json([
            'status' => true,
            'message' => "User modifié avec succès",
            'user' => $user
        ]);
    }
}
