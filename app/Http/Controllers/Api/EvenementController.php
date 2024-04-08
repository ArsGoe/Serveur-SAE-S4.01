<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EvenementResource;
use App\Models\Enums\UserRole;
use App\Models\Evenement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EvenementController extends Controller
{
    public function index(Request $request) : AnonymousResourceCollection
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

       return EvenementResource::collection($evenements);
    }
}
