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
        Schema::table('service_providers', function (Blueprint $table) {
            if (!Schema::hasColumn('service_providers', 'operation_stage_id')) {
                $table->foreignId('operation_stage_id')->nullable()->after('id')->constrained()->onDelete('set null');
            }
            if (Schema::hasColumn('service_providers', 'service_type_id')) {
                $table->dropColumn('service_type_id');
            }
        });

        // Drop service_types table if requested
        Schema::dropIfExists('service_types');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_providers', function (Blueprint $table) {
            $table->dropForeign(['operation_stage_id']);
            $table->dropColumn('operation_stage_id');
            $table->foreignId('service_type_id')->nullable()->constrained();
        });
    }
};
