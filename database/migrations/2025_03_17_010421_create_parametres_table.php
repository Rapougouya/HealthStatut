<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParametresTable extends Migration
{
    public function up()
    {
        Schema::create('parametres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('heart_rate_critical_min')->nullable();
            $table->integer('heart_rate_critical_max')->nullable();
            $table->integer('heart_rate_warning_min')->nullable();
            $table->integer('heart_rate_warning_max')->nullable();
            $table->float('temperature_critical_min')->nullable();
            $table->float('temperature_critical_max')->nullable();
            $table->float('temperature_warning_min')->nullable();
            $table->float('temperature_warning_max')->nullable();
            $table->boolean('notif_alertes_critiques')->default(false);
            $table->boolean('notif_alertes_hautes')->default(false);
            $table->boolean('notif_alertes_moyennes')->default(false);
            $table->boolean('email_alertes_critiques')->default(false);
            $table->boolean('email_rapports_quotidiens')->default(false);
            $table->boolean('email_mises_a_jour')->default(false);
            $table->string('theme')->default('light');
            $table->boolean('compact_mode')->default(false);
            $table->integer('items_per_page')->default(10);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('parametres');
    }
}