<?php

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
    Schema::create('seuils', function (Blueprint $table) {
        $table->id();
        $table->string('nom'); // Nom du seuil
        $table->float('valeur_min')->nullable(); // Valeur minimale
        $table->float('valeur_max')->nullable(); // Valeur maximale
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seuils');
    }
};
