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
        Schema::table('news', function (Blueprint $table) {
                        if (!Schema::hasColumn('news', 'image')) {
                $table->string('image')->nullable();
            }
            if (!Schema::hasColumn('news', 'sentiment')) {
                $table->enum('sentiment', ['Positive', 'Neutral', 'Negative'])->default('Neutral');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
                        if (Schema::hasColumn('news', 'image') || Schema::hasColumn('news', 'sentiment')) {
                $table->dropColumn(['image','sentiment']);
            }
        });
    }
};
