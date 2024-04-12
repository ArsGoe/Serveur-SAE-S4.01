<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Client",
    title: "Client",
    description: "Represents a client",
    properties: [
        new OA\Property(property: "id", description: "Client's identifier", type: "integer"),
        new OA\Property(property: "nom", description: "Client's name", type: "string"),
        new OA\Property(property: "prenom", description: "Client's first name", type: "string"),
        new OA\Property(property: "avatar", description: "Client's avatar", type: "string"),
        new OA\Property(property: "adresse", description: "Client's address", type: "string"),
        new OA\Property(property: "code_postal", description: "Client's postal code", type: "string"),
        new OA\Property(property: "ville", description: "Client's city", type: "string"),
        new OA\Property(property: "user_id", description: "Identifier of the user associated with the client",
            type: "integer"),
    ]
)]
class Client extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = ['nom', 'prenom', 'email', 'telephone', 'adresse', 'code_postal', 'ville', 'pays'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
