<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

//use App\Http\Requests\UserRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\ClientResource;
use App\Http\Resources\UserResource;
use App\Models\Client;
use App\Models\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller {
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }


    public function login(Request $request) {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ], [
            'required' => 'Le champ :attribute est obligatoire',
            'email' => 'L\'adresse mail n\'est pas correcte',
            'min' => 'Le champ :attribute doit contenir au minimum :min caractères.',
        ]);
        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        return response()->json([
            'status' => 'success',
            'message' => 'BONJOUR, JE SUIS LE ROI DRAGON',
            'user' => new UserResource($user),
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }


    public function register(UserRequest $request) {
        $user = User::create([
            'name' => $request->nom." ".$request->prenom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $client = new Client();
        $client->nom = $request->nom;
        $client->prenom = $request->prenom;
        $client->adresse = $request->adresse;
        $client->code_postal = $request->code_postal;
        $client->ville = $request->ville;
        $client->avatar = "https://www.intima-et-moi.fr/mon-hygiene-intime/quel-est-le-ph-de-la-zone-intime/";
        $client->user_id = User::findOrFail(User::findbyemail($user->email))->id;
        $client->save();
//        $roleVisiteur = UserRole::where('nom', UserRole::NON_ACTIF)->first();
            $user->role =  UserRole::ACTIF;
//        $user->roles()->attach([$roleVisiteur->id]);
        $token = auth()->tokenById($user->id);
        return response()->json([
            'status' => 'success',
            'message' => 'Bienvenue dans notre communauté',
            'user' => new UserResource($user),
            'client' => new ClientResource($client),
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }


    public function logout() {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }


    public function refresh() {
        return response()->json([
            'status' => 'success',
            'user' => new UserResource(Auth::user()),
            'authorisation' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ]);
    }


    public function me() {
        return response()->json([
            'status' => 'success',
            'user' => new UserResource(Auth::user()),
            'client' => new ClientResource(Client::all()->where('user_id',Auth::user()['id']))
        ]);
    }
}
