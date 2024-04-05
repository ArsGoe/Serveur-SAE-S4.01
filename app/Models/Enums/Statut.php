<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statut extends Model
{
    const EN_ATTENTE = 'EN_ATTENTE';
    const PAYE = 'PAYE';
    const ANNULE = 'ANNULE';
    const BILLET_EDITE = 'BILLET_EDITE';


    public static function getValues(): array
    {
        return [
            self::EN_ATTENTE,
            self::PAYE,
            self::ANNULE,
            self::BILLET_EDITE
        ];
    }
}
