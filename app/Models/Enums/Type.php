<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    const THEATRE = 'THEATRE';
    const CINEMA = 'CINEMA';
    const CONCERT = 'CONCERT';
    const FESTIVAL = 'FESTIVAL';
    const COMPETITION = 'COMPETITION';


    public static function getValues(): array
    {
        return [
            self::THEATRE,
            self::CINEMA,
            self::CONCERT,
            self::FESTIVAL,
            self::COMPETITION,
        ];
    }
}

