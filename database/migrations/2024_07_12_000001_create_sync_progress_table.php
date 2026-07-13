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
        Schema::create('sync_progress', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id')->unique();
            $table->string('stage')->default('countries'); // countries, weather, economy, currency, news
            $table->integer('total_countries')->default(0);
            $table->integer('processed_countries')->default(0);
            $table->integer('current_batch')->default(0);
            $table->integer('total_batches')->default(0);
            $table->integer('progress_percentage')->default(0);
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_progress');
    }
};
