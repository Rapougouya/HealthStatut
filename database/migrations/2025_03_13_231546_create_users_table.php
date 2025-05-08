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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->unique();
            $table->string('password');
            $table->foreignId('role_id')->constrained('roles');
            $table->foreignId('service_id')->nullable()->constrained('services');
            $table->string('theme')->default('light');
            $table->boolean('compact_mode')->default(false);
            $table->integer('items_per_page')->default(10);
            $table->boolean('notif_alertes_critiques')->default(true);
            $table->boolean('notif_alertes_hautes')->default(true);
            $table->boolean('notif_alertes_moyennes')->default(true);
            $table->boolean('email_alertes_critiques')->default(true);
            $table->boolean('email_rapports_quotidiens')->default(true);
            $table->boolean('email_mises_a_jour')->default(true);
            $table->string('avatar')->nullable();
            $table->timestamp('derniere_connexion')->nullable();
            $table->enum('statut', ['actif', 'inactif', 'suspendu']);

            // Colonnes manquantes ajoutées ici
            $table->enum('sexe', ['Homme', 'Femme', 'Autre'])->nullable();
            $table->date('date_naissance')->nullable();
            $table->string('telephone')->nullable();
            $table->string('adresse')->nullable();
            $table->integer('taille')->nullable(); // en cm
            $table->integer('poids')->nullable();  // en kg

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
