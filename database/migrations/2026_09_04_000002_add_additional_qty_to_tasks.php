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
        Schema::table('tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('tasks', 'is_additional')) {
                $table->tinyInteger('is_additional')->default(0)->after('stage_id');
            }
            if (!Schema::hasColumn('tasks', 'job_card_fabric_detail_id')) {
                $table->unsignedBigInteger('job_card_fabric_detail_id')->nullable()->after('is_additional');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'is_additional')) {
                $table->dropColumn('is_additional');
            }
            if (Schema::hasColumn('tasks', 'job_card_fabric_detail_id')) {
                $table->dropColumn('job_card_fabric_detail_id');
            }
        });
    }
};
