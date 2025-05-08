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
        Schema::create('rapports', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('type'); // quotidien, hebdomadaire, mensuel, spécial
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('cree_par')->constrained('users');
            $table->json('donnees'); // données du rapport au format JSON
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
