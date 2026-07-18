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
        Schema::table('risk_scores', function (Blueprint $table) {
            // Change enum to include 'Critical' (used by RiskScoreEngine)
            $table->enum('risk_level', ['Low', 'Medium', 'High', 'Critical'])
                  ->default('Low')
                  ->change();
            
            // Add indexes for performance (country_id already has FK index)
            if (!Schema::hasIndex('risk_scores', 'risk_scores_total_score_index')) {
                $table->index('total_score');
            }
            if (!Schema::hasIndex('risk_scores', 'risk_scores_risk_level_index')) {
                $table->index('risk_level');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('risk_scores', function (Blueprint $table) {
            $table->dropIndex(['total_score']);
            $table->dropIndex(['risk_level']);
            
            // Revert enum to original
            $table->enum('risk_level', ['Low', 'Medium', 'High'])
                  ->default('Low')
                  ->change();
        });
    }
};
