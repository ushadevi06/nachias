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
                if (!Schema::hasColumn('job_card_fabric_details', 'additional_batch_no')) {
                    $table->string('additional_batch_no', 50)->nullable()->after('is_additional');
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
                if (Schema::hasColumn('job_card_fabric_details', 'additional_batch_no')) {
                    $table->dropColumn('additional_batch_no');
                }
            });
        }
    }
};
