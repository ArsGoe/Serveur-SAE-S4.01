<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'Reservation', title: 'Reservation', description: 'A reservation for an event.',
    properties: [
        new OA\Property(property: "id", type: "integer", format: "int64"),
        new OA\Property(property: "date_res", type: "date"),
        new OA\Property(property: "nb_billets", type: "integer"),
        new OA\Property(property: "montant", type: "double"),
        new OA\Property(property: "statut", type: "string"),
        new OA\Property(property: "evenement_id", type: "integer"),
        new OA\Property(property: "client_id", type: "integer")
])]
/**
 * @method static find(int $id)
 */
class Reservation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = ['id'];

    public function evenement(): BelongsTo
    {
        return $this->belongsTo(Evenement::class);
    }

    public function billets(): HasMany
    {
        return $this->Hasmany(Billet::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
