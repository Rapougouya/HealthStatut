<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLaboratoiresTable extends Migration
{
    public function up()
    {
        Schema::create('laboratoires', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->string('titre');
            $table->string('type');
            $table->date('date');
            $table->string('laboratoire');
            $table->text('resultat');
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('laboratoires');
    }
}
