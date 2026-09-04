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
        if (Schema::hasTable('job_card_fabric_details') && Schema::hasColumn('job_card_fabric_details', 'lot_no')) {
            Schema::table('job_card_fabric_details', function (Blueprint $table) {
                $table->dropColumn('lot_no');
            });
        }

        if (Schema::hasTable('job_card_cutting_size_ratios') && Schema::hasColumn('job_card_cutting_size_ratios', 'lot_no')) {
            Schema::table('job_card_cutting_size_ratios', function (Blueprint $table) {
                $table->dropColumn('lot_no');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('job_card_fabric_details') && !Schema::hasColumn('job_card_fabric_details', 'lot_no')) {
            Schema::table('job_card_fabric_details', function (Blueprint $table) {
                $table->integer('lot_no')->default(0)->after('job_card_entry_id');
            });
        }

        if (Schema::hasTable('job_card_cutting_size_ratios') && !Schema::hasColumn('job_card_cutting_size_ratios', 'lot_no')) {
            Schema::table('job_card_cutting_size_ratios', function (Blueprint $table) {
                $table->integer('lot_no')->default(0)->after('job_card_entry_id');
            });
        }
    }
};
