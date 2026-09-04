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
        if (Schema::hasTable('job_card_fabric_details')) {
            Schema::table('job_card_fabric_details', function (Blueprint $table) {
                if (!Schema::hasColumn('job_card_fabric_details', 'is_additional')) {
                    $table->tinyInteger('is_additional')->default(0)->after('job_card_entry_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('job_card_fabric_details')) {
            Schema::table('job_card_fabric_details', function (Blueprint $table) {
                if (Schema::hasColumn('job_card_fabric_details', 'is_additional')) {
                    $table->dropColumn('is_additional');
                }
            });
        }
    }
};
