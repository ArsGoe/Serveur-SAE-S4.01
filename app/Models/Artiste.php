<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artiste extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = ['id'];

    public function evenements()
    {
        return $this->belongsToMany(Evenement::class);
    }
}
