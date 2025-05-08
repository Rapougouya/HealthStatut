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
        Schema::create('signes_vitaux', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('capteur_id')->constrained('capteurs')->onDelete('cascade');
            $table->decimal('rythme_cardiaque', 5, 2)->nullable(); // bpm
            $table->decimal('temperature', 5, 2)->nullable(); // °C
            $table->integer('pression_arterielle_systolique')->nullable(); // mmHg
            $table->integer('pression_arterielle_diastolique')->nullable(); // mmHg
            $table->decimal('saturation_oxygene', 5, 2)->nullable(); // pourcentage
            $table->integer('frequence_respiratoire')->nullable(); // respirations par minute
            $table->decimal('glucose', 5, 2)->nullable(); // taux de glucose
            $table->timestamp('enregistre_a');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vital_signs');
    }
};
