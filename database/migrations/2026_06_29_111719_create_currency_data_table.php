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
        Schema::create('currency_data', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel countries
            $table->foreignId('country_id')
                  ->constrained('countries')
                  ->onDelete('cascade');

            // Informasi mata uang
            $table->string('currency_code', 10);          // USD, IDR, EUR
            $table->string('currency_name')->nullable();  // Dollar, Rupiah, Euro
            $table->string('base_currency', 10)->default('USD');

            // Nilai tukar
            $table->decimal('exchange_rate', 15, 6);

            // Perubahan kurs
            $table->decimal('change_percentage', 8, 2)->nullable();

            // Waktu update dari API
            $table->timestamp('last_updated')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currency_data');
    }
};