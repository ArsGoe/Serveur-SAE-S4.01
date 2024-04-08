<?php

use App\Models\Prix;
use App\Models\Reservation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('billets', function (Blueprint $table) {
            $table->id();
            $table->integer('quantite');
            $table->foreignIdFor(Reservation::class)->constrained()->onDelete('cascade');
            $table->foreignId('prix_id')->references('id')->on('prix')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billets');
    }
};
