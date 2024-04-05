<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Client extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = ['nom', 'prenom', 'email', 'telephone', 'adresse', 'code_postal', 'ville', 'pays'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
