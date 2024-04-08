<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'statut' => $this->statut,
            'date_res' => $this->date_res,
            'billets' => BilletResource::collection($this->whenLoaded('billets')),
            'montant' => $this->montant,
        ];
    }
}
