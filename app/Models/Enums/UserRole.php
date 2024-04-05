<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    const ADMIN = 'ADMIN';
    const GESTIONNAIRE = 'GESTIONNAIRE';
    const ACTIF = 'ACTIF';
    const NON_ACTIF = 'NON_ACTIF';

    public static function getValues(): array
    {
        return [
            self::ADMIN,
            self::GESTIONNAIRE,
            self::ACTIF,
            self::NON_ACTIF,
        ];
    }
}
