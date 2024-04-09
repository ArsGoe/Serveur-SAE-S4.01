<?php

use App\Models\Evenement;
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
        Schema::create('prix', function (Blueprint $table) {
            $table->id();
            $table->string('categorie');
            $table->integer('nombre');
            $table->double('valeur');
            $table->foreignIdFor(Evenement::class)->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prix');
    }
};
