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
        Schema::table('production_receipts', function (Blueprint $table) {
            if (!Schema::hasColumn('production_receipts', 'is_additional')) {
                $table->tinyInteger('is_additional')->default(0)->after('job_card_id');
            }
            if (!Schema::hasColumn('production_receipts', 'job_card_fabric_detail_id')) {
                $table->unsignedBigInteger('job_card_fabric_detail_id')->nullable()->after('is_additional');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_receipts', function (Blueprint $table) {
            if (Schema::hasColumn('production_receipts', 'is_additional')) {
                $table->dropColumn('is_additional');
            }
            if (Schema::hasColumn('production_receipts', 'job_card_fabric_detail_id')) {
                $table->dropColumn('job_card_fabric_detail_id');
            }
        });
    }
};
