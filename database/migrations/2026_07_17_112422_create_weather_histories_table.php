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
        Schema::create('weather_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->decimal('temperature', 8, 2);
            $table->decimal('wind_speed', 8, 2);
            $table->decimal('rainfall', 8, 2);
            $table->decimal('humidity', 8, 2);
            $table->decimal('cloud', 8, 2)->nullable();
            $table->decimal('pressure', 8, 2)->nullable();
            $table->string('weather_condition');
            $table->integer('storm_risk');
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index(['country_id', 'recorded_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_histories');
    }
};
