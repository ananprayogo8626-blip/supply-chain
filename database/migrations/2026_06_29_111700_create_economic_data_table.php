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
        Schema::create('economic_data', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel countries
            $table->foreignId('country_id')
                  ->constrained('countries')
                  ->onDelete('cascade');

            // Data ekonomi
            $table->decimal('gdp', 18, 2)->nullable()->comment('Gross Domestic Product');
            $table->decimal('inflation', 8, 2)->nullable()->comment('Inflasi (%)');
            $table->decimal('exports', 18, 2)->nullable()->comment('Nilai ekspor');
            $table->decimal('imports', 18, 2)->nullable()->comment('Nilai impor');
            $table->bigInteger('population')->nullable()->comment('Jumlah penduduk');

            // Tahun data ekonomi
            $table->year('data_year')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('economic_data');
    }
};