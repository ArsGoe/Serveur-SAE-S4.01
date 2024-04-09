<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property mixed $id
 */
class Artiste extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = ['id'];

    public function evenements(): BelongsToMany
    {
        return $this->belongsToMany(Evenement::class, 'participants', 'artiste_id', 'evenement_id')
            ->withPivot('ordre');
    }
}
