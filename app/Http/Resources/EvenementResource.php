<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property integer $id
 * @property string $titre
 * @property string $type
 * @property string $description
 * @property mixed $date_event
 * @property mixed $lieu
 * @property mixed $artistes
 */
class EvenementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'type' => $this->type,
            'description' => $this->description,
            'date_event' => $this->date_event,
            'lieu' => $this->lieu
        ];
    }
}
