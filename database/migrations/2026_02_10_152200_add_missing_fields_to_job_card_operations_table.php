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
        Schema::table('job_card_operations', function (Blueprint $table) {
            if (!Schema::hasColumn('job_card_operations', 'service_provider_id')) {
                $table->unsignedBigInteger('service_provider_id')->nullable()->after('operation_stage_id');
            }
            if (!Schema::hasColumn('job_card_operations', 'deadline_date')) {
                $table->date('deadline_date')->nullable()->after('assigned_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_card_operations', function (Blueprint $table) {
            $table->dropColumn(['service_provider_id', 'deadline_date']);
        });
    }
};
