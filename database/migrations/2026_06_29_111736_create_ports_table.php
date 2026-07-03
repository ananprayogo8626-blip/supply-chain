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
        Schema::create('ports', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel countries
            $table->foreignId('country_id')
                  ->constrained('countries')
                  ->onDelete('cascade');

            // Informasi pelabuhan
            $table->string('port_name');
            $table->string('port_code')->nullable();
            $table->string('city')->nullable();

            // Koordinat
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            // Informasi tambahan
            $table->string('port_type')->nullable();      // Seaport, River Port, dll.
            $table->string('status')->default('Active');  // Active / Inactive

            // Deskripsi
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ports');
    }
};