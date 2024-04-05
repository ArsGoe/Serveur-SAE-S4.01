<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Billet extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function prix(): BelongsTo
    {
        return $this->belongsTo(Prix::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }
}
