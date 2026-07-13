<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sentiment_words', function (Blueprint $table) {
            $table->id();
            $table->string('word')->unique();
            $table->enum('type', ['positive', 'negative']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sentiment_words');
    }
};
