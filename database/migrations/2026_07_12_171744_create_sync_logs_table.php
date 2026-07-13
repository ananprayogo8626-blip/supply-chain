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
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id')->nullable()->index();
            $table->string('stage')->nullable(); // countries, weather, economy, currency, news
            $table->foreignId('country_id')->nullable();
            $table->string('country_code', 10)->nullable();
            $table->text('error_message')->nullable();
            $table->string('exception_class')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamps();
            
            $table->index(['batch_id', 'stage']);
            $table->index('country_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_logs');
    }
};
