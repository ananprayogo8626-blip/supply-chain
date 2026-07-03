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
        Schema::create('weather_data', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel countries
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');

            // Data cuaca
            $table->decimal('temperature', 8, 2)->nullable()->comment('Suhu dalam Celcius');
            $table->decimal('wind_speed', 8, 2)->nullable()->comment('Kecepatan angin km/h');
            $table->decimal('rainfall', 8, 2)->nullable()->comment('Curah hujan mm');
            $table->integer('humidity')->nullable()->comment('Kelembapan %');
            $table->string('weather_condition')->nullable()->comment('Sunny, Rainy, Cloudy, dll');
            $table->integer('storm_risk')->default(0)->comment('0-100');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_data');
    }
};