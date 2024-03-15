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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('reference_url');
            $table->string('mark');
            $table->string('model');
            $table->string('year');
            $table->string('motor');
            $table->string('fuel_type');
            $table->string('gearbox');
            $table->string('color');
            $table->string('body_type');
            $table->string('mileage_in_km');
            $table->string('technical_inspection_date');
            $table->integer('price_in_cents');
            $table->dateTime('upload_date');
            $table->json('specifications')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
