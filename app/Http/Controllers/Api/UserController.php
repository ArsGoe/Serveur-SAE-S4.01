<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        $client = $user->client();
        return response()->json([
            'status' => true,
            'message' => $client->nom + $client->prenom +$client->adresse +$client->code_postal +$client->ville + $user->name + $user->email + $user->role,
        ]);
    }

}
