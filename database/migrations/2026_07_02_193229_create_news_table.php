<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {

            $table->id();

            $table->foreignId('country_id')
                  ->constrained('countries')
                  ->onDelete('cascade');

            $table->string('title');

            $table->string('source');

            $table->string('category')->nullable();

            $table->string('url')->nullable();

            $table->integer('impact_score')->default(0);

            $table->text('summary')->nullable();

            $table->timestamp('published_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};