<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('alertes', function (Blueprint $table) {
            // Ajoute la colonne avec clé étrangère
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained()  // Lie automatiquement à la table 'users'
                  ->onDelete('cascade');  // Supprime les alertes si l'utilisateur est supprimé
        });
    }

    public function down()
    {
        Schema::table('alertes', function (Blueprint $table) {
            // Supprime proprement la colonne
            $table->dropForeign(['user_id']);  // D'abord la contrainte
            $table->dropColumn('user_id');     // Puis la colonne
        });
    }
};