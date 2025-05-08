<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitesTable extends Migration
{
    public function up()
    {
        Schema::create('activites', function (Blueprint $table) {
            $table->id(); // Colonne id
            $table->string('type'); // Type d'activité (ex: "patient", "alerte", etc.)
            $table->string('icon'); // Icône associée à l'activité
            $table->string('title'); // Titre de l'activité
            $table->text('description'); // Description détaillée
            $table->foreignId('user_id')->constrained('users'); // Clé étrangère vers la table des utilisateurs
            $table->timestamps(); // Colonnes created_at et updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('activites');
    }
}