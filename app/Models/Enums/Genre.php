<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    const POP = 'POP';
    const ROCK = 'ROCK';
    const CLASSIQUE = 'CLASSIQUE';
    const RAP = 'RAP';
    const FOLK = 'FOLK';
    const SPORTIF = 'SPORTIF';
    const COMEDIEN = 'COMEDIEN';
    const VARIETE = 'VARIETE';


    public static function getValues(): array
    {
        return [
            self::POP,
            self::ROCK,
            self::CLASSIQUE,
            self::RAP,
            self::FOLK,
            self::SPORTIF,
            self::COMEDIEN,
            self::VARIETE
        ];
    }
}
