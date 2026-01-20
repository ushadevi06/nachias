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
        Schema::table('job_card_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('job_card_entries', 'service_provider_id')) {
                $table->unsignedBigInteger('service_provider_id')->nullable()->after('purchase_order_id');
                $table->foreign('service_provider_id')->references('id')->on('service_providers');
            }
            if (!Schema::hasColumn('job_card_entries', 'issue_store_id')) {
                // If store_types.id is INT, use unsignedInteger
                $table->unsignedInteger('issue_store_id')->nullable()->after('service_provider_id');
                $table->foreign('issue_store_id')->references('id')->on('store_types');
            }
            if (!Schema::hasColumn('job_card_entries', 'receipt_store_id')) {
                // If store_types.id is INT, use unsignedInteger
                $table->unsignedInteger('receipt_store_id')->nullable()->after('issue_store_id');
                $table->foreign('receipt_store_id')->references('id')->on('store_types');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_card_entries', function (Blueprint $table) {
            $table->dropForeign(['service_provider_id']);
            $table->dropForeign(['issue_store_id']);
            $table->dropForeign(['receipt_store_id']);
            $table->dropColumn(['service_provider_id', 'issue_store_id', 'receipt_store_id']);
        });
    }
};
