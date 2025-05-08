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
        Schema::create('alertes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('capteur_id')->nullable()->constrained('capteurs')->onDelete('set null');
            $table->foreignId('signe_vital_id')->nullable()->constrained('signes_vitaux')->onDelete('set null');
            $table->string('type'); // critique, attention, information
            $table->string('message');
            $table->enum('statut', ['nouvelle', 'vue', 'traitee', 'ignoree']);
            $table->enum('severite', ['basse', 'moyenne', 'haute', 'critique']);
            $table->json('settings')->nullable(); // Stockage des paramètres au format JSON
            $table->foreignId('confirmee_par')->nullable()->constrained('users');
            $table->timestamp('confirmee_a')->nullable();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       
       Schema::table('alertes');
    }
};
