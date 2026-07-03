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
        Schema::create('risk_scores', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel countries
            $table->foreignId('country_id')
                  ->constrained('countries')
                  ->onDelete('cascade');

            // Nilai setiap indikator
            $table->integer('weather_score')->default(0);
            $table->integer('economic_score')->default(0);
            $table->integer('currency_score')->default(0);
            $table->integer('news_score')->default(0);
            $table->integer('port_score')->default(0);

            // Total skor
            $table->integer('total_score')->default(0);

            // Level risiko
            $table->enum('risk_level', [
                'Low',
                'Medium',
                'High'
            ])->default('Low');

            // Catatan hasil analisis
            $table->text('recommendation')->nullable();

            // Waktu data diambil
            $table->timestamp('calculated_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risk_scores');
    }
};