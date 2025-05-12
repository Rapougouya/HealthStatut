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
        Schema::create('sensor_readings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('capteur_id');  // Utilisation de sensor_id au lieu de capteur_id
            $table->float('value');
            $table->timestamp('timestamp');
            $table->json('raw_data')->nullable();
            $table->integer('signal_strength')->nullable(); // Force du signal WiFi en dBm
            $table->integer('battery_level')->nullable(); // Niveau de batterie en pourcentage
            $table->integer('status_code')->nullable(); // Code de statut pour diagnostiquer les problèmes
            $table->string('connection_type')->nullable()->default('wifi'); // Type de connexion (WiFi, Bluetooth, etc.)
            $table->integer('latency')->nullable(); // Latence de la connexion en ms
            $table->timestamps();
            
            // Ajout de la clé étrangère
            $table->foreign('capteur_id')->references('id')->on('capteurs')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_readings');
    }
};