<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prix extends Model
{
    use HasFactory;

    protected $table = 'prix';
    public $timestamps = false;

    protected $guarded = ['id'];

    public function evenement(): BelongsTo
    {
        return $this->belongsTo(Evenement::class);
    }
}
