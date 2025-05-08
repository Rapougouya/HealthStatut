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
    // Table des catégories de services
    Schema::create('service_categories', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->text('description')->nullable();
        $table->timestamps();
    });

    // Table principale des services
    Schema::create('services', function (Blueprint $table) {
        $table->id();
        $table->foreignId('category_id')->nullable()->constrained('service_categories')->nullOnDelete();
        $table->string('name');
        $table->text('description')->nullable();
        $table->decimal('cost', 10, 2)->default(0);
        $table->integer('duration_minutes')->default(30);
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });

}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
        Schema::dropIfExists('service_categories');
    }
};
