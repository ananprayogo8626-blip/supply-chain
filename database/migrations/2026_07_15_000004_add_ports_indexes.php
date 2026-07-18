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
        Schema::table('ports', function (Blueprint $table) {
            // Add indexes for performance
            if (!Schema::hasIndex('ports', 'ports_port_name_index')) {
                $table->index('port_name');
            }
            if (!Schema::hasIndex('ports', 'ports_city_index')) {
                $table->index('city');
            }
            if (!Schema::hasIndex('ports', 'ports_status_index')) {
                $table->index('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ports', function (Blueprint $table) {
            $table->dropIndex(['port_name']);
            $table->dropIndex(['city']);
            $table->dropIndex(['status']);
        });
    }
};
