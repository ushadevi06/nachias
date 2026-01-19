<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            DB::statement("ALTER TABLE purchase_orders CHANGE sales_agent_id purchase_commission_agent_id BIGINT UNSIGNED DEFAULT NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            DB::statement("ALTER TABLE purchase_orders CHANGE purchase_commission_agent_id sales_agent_id BIGINT UNSIGNED DEFAULT NULL");
        });
    }
};
