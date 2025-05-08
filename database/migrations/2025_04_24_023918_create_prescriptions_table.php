<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrescriptionsTable extends Migration
{
    /**
     * Exécuter la migration.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();  // Clé primaire auto-incrémentée
            $table->unsignedBigInteger('patient_id');  // Colonne pour la relation avec Patient
            $table->string('categorie');  // Catégorie de la prescription
            $table->string('statut');  // Statut de la prescription
            $table->string('medicament');  // Médicament prescrit
            $table->string('posologie');  // Posologie du médicament
            $table->date('date_debut');  // Date de début de prescription
            $table->date('date_fin');  // Date de fin de prescription
            $table->string('medecin');  // Nom du médecin
            $table->timestamps();  // Les champs created_at et updated_at

            // Clé étrangère pour la relation avec la table patients
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
        });
    }

    /**
     * Annuler la migration.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prescriptions');
    }
}
