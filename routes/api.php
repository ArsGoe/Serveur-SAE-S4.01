<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\EvenementController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login')->name('login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
    Route::get('me', 'me');
});


// Route::apiResource('salles', SalleController::class);

Route::prefix('clients')->group(function () {
    Route::get('/', [ClientController::class, 'index'])
        ->middleware(['auth', 'role:Gestionnaire'])
        ->name('clients.index ');
    Route::get('/trimNom', [ClientController::class, 'trimNom'])
        ->middleware(['auth', 'role:Gestionnaire'])
        ->name('clients.TrimNom');
    Route::get('/triNom', [ClientController::class, 'triNom'])
        ->middleware(['auth', 'role:Gestionnaire'])
        ->name('clients.TriNom');
    Route::get('/triVille', [ClientController::class, 'triVille'])
        ->middleware(['auth', 'role:Gestionnaire'])
        ->name('clients.TriVille');
    Route::get('/{id}', [ClientController::class, 'show'])->where('id', '[0-9]+')
        ->middleware(['auth'])
        ->name('clients.show');
    Route::put('/{id}', [ClientController::class, 'update'])->where('id', '[0-9]+')
        ->middleware(['auth', 'role:Gestionnaire'])
        ->name('clients.update');
    Route::post('/', [ClientController::class, 'store'])
        ->name('clients.store');
    Route::delete('/{id}', [ClientController::class, 'destroy'])->where('id', '[0-9]+')
        ->middleware(['auth', 'role:admin'])
        ->name('clients.destroy');
});

Route::prefix('users')->group(function () {
    Route::get('/{id}', [UserController::class, 'profil'])->where('id', '[0-9]+')
        ->middleware(['auth'])
        ->name('users.profil ');
    Route::delete('/{id}', [UserController::class, 'destroy'])->where('id', '[0-9]+')
        ->middleware(['auth', 'role:admin'])
        ->name('users.destroy ');
});

Route::prefix('evenements')->group(function () {
   Route::get("/", [EvenementController::class, 'index'])
        ->name('evenements.index');
   Route::get("/{id}", [EvenementController::class, 'show'])->where('id', '[0-9]+')
        ->middleware(['auth'])
        ->name('evenements.show');
   Route::post("/", [EvenementController::class, 'store'])
        ->middleware(['auth'])
        ->name('evenements.store');
   Route::delete("/{id}", [EvenementController::class, 'destroy'])->where('id', '[0-9]+')
        ->middleware(['auth'])
        ->name('evenements.destroy');
   Route::get("/{id}/prix", [EvenementController::class, 'prix'])->where('id', '[0-9]+')
        ->middleware(['auth'])
        ->name('evenements.prix');
   Route::put("/{id}", [EvenementController::class, 'update'])->where('id', '[0-9]+')
        ->middleware(['auth'])
        ->name('evenements.update');
   Route::put("/{id}/prix", [EvenementController::class, 'updatePrix'])->where('id', '[0-9]+')
        ->middleware(['auth'])
        ->name('evenements.updatePrix');
   Route::put("/{id}/artistes", [EvenementController::class, 'updateArtistes'])->where('id', '[0-9]+')
        ->middleware(['auth'])
        ->name('evenements.updateArtistes');
    Route::get('/{id}/reservations', [ReservationController::class, 'reservationsEvent'])
        ->where('id', '[0-9]+')
        ->middleware(['auth', 'role:Gestionnaire,admin'])
        ->name('reservations.reservationsEvent');
});

Route::prefix('reservations')->group(function () {
    Route::get('/', [ReservationController::class, 'reservationsClient'])
        ->middleware(['auth', 'role:ACTIF'])
        ->name('reservations.reservationsClient');
    Route::post('/', [ReservationController::class, 'store'])
        ->middleware(['auth', 'role:ACTIF,ADMIN,GESTIONNAIRE'])
        ->name('reservations.store');
});

Route::get('/lieux', [EvenementController::class, 'lieux'])
    ->middleware(['auth'])
    ->name('lieux.index');
