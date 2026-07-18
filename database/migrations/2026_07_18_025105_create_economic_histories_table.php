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
        Schema::create('economic_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
            $table->double('gdp', 20, 2)->nullable();
            $table->double('gdp_growth', 8, 2)->nullable();
            $table->double('inflation', 8, 2)->nullable();
            $table->double('exports', 20, 2)->nullable();
            $table->double('imports', 20, 2)->nullable();
            $table->bigInteger('population')->nullable();
            $table->integer('data_year')->nullable();
            $table->timestamp('recorded_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('economic_histories');
    }
};
