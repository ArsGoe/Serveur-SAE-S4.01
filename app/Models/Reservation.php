<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = ['id'];

    public function evenement(): BelongsTo
    {
        return $this->belongsTo(Evenement::class);
    }
}
