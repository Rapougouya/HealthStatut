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
        Schema::create('seuils_alerte', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('capteur_id')->constrained('capteurs')->onDelete('cascade');
            $table->string('type_mesure'); // rythme_cardiaque, temperature, etc.
            $table->decimal('valeur_min', 8, 2)->nullable();
            $table->decimal('valeur_max', 8, 2)->nullable();
            $table->enum('severite', ['basse', 'moyenne', 'haute', 'critique']);
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients_table_seuil_alerte');
    }
};
