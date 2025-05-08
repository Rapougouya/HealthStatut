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
        Schema::create('capteurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('type'); // rythme_cardiaque, temperature, etc.
            $table->string('numero_serie')->unique();
            $table->string('modele');
            $table->enum('statut', ['actif', 'inactif', 'maintenance', 'erreur']);
            $table->foreignId('patient_id')->nullable()->constrained('patients')->onDelete('set null');
            $table->date('derniere_maintenance')->nullable();
            $table->integer('niveau_batterie')->default(100); // pourcentage
            $table->string('adresse_mac')->nullable();
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

    Schema::dropIfExists('signes_vitaux'); // d'abord la table dépendante
    Schema::dropIfExists('capteurs');      // ensuite la table principale

    Schema::enableForeignKeyConstraints();
    }
};
