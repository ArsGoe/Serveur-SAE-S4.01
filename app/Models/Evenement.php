<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property mixed $id
 * @property mixed $prix
 * @method static find(int $id)
 */
class Evenement extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = ['id'];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function artistes(): BelongsToMany
    {
        return $this->belongsToMany(Artiste::class, 'participants', 'evenement_id', 'artiste_id')
            ->withPivot('ordre');
    }

    public function lieu(): BelongsTo
    {
        return $this->belongsTo(Lieu::class);
    }

    public function prix(): HasMany
    {
        return $this->hasMany(Prix::class);
    }
}
