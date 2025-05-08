<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('service_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_appointment_id')->constrained()->cascadeOnDelete();
            $table->json('result_data')->nullable();
            $table->text('attachments')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_results');
    }
};
