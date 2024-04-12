<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Lieu",
    title: "Location",
    description: "A place where events take place",
    properties: [
        new OA\Property(property: "id", description: "Location ID", type: "integer"),
        new OA\Property(property: "nom", description: "Location name", type: "string"),
        new OA\Property(property: "adresse", description: "Location address", type: "string"),
        new OA\Property(property: "ville", description: "Location city", type: "string"),
        new OA\Property(property: "lat", description: "Latitude", type: "double"),
        new OA\Property(property: "long", description: "Longitude", type: "double"),
    ]
)]
class Lieu extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'lieux';
    protected $guarded = ['id'];
    protected $fillable = ['nom', 'adresse', 'ville', 'cp', 'capacite'];

    public function evenements(): HasMany
    {
        return $this->hasMany(Evenement::class);
    }
}
