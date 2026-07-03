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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();

            // Penulis artikel (Admin/User)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Informasi artikel
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary')->nullable();
            $table->longText('content');

            // Gambar artikel
            $table->string('thumbnail')->nullable();

            // Kategori artikel
            $table->enum('category', [
                'Economy',
                'Logistics',
                'Shipping',
                'Weather',
                'Geopolitics',
                'Supply Chain',
                'Other'
            ])->default('Other');

            // Status artikel
            $table->enum('status', [
                'Draft',
                'Published'
            ])->default('Draft');

            // Tanggal publikasi
            $table->timestamp('published_at')->nullable();

            // Jumlah dilihat
            $table->unsignedInteger('views')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};