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
        Schema::create('watchlists', function (Blueprint $table) {

            $table->id();

            // Relasi ke user
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Relasi ke negara
            $table->foreignId('country_id')
                  ->constrained('countries')
                  ->onDelete('cascade');

            // Nama perusahaan yang dipantau
            $table->string('company_name');

            // Bidang industri
            $table->string('industry')->nullable();

            // Prioritas monitoring
            $table->integer('priority')->default(1);

            // Status monitoring
            $table->enum('status', [
                'Monitoring',
                'Critical',
                'Resolved'
            ])->default('Monitoring');

            // Status aktif
            $table->boolean('is_active')->default(true);

            // Catatan
            $table->text('notes')->nullable();

            $table->timestamps();

            // Mencegah user menyimpan perusahaan/negara yang sama dua kali
            $table->unique(['user_id', 'country_id', 'company_name']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('watchlists');
    }
};