<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lieu extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'lieux';
    protected $guarded = ['id'];
    protected $fillable = ['nom', 'adresse', 'ville', 'cp', 'capacite'];

    public function evenements(): HasMany
    {
        return $this->hasMany(Evenement::class);
    }
}
