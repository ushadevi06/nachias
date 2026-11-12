<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('stock_taking_items')) {
            Schema::table('stock_taking_items', function (Blueprint $table) {
                if (!Schema::hasColumn('stock_taking_items', 'system_store_location_id')) {
                    $table->unsignedBigInteger('system_store_location_id')->nullable()->after('sleeve_type')->index();
                }
                if (!Schema::hasColumn('stock_taking_items', 'store_location_id')) {
                    $table->unsignedBigInteger('store_location_id')->nullable()->after('system_store_location_id')->index();
                }
            });
        }

        if (Schema::hasTable('stock_taking_scan_logs')) {
            Schema::table('stock_taking_scan_logs', function (Blueprint $table) {
                if (!Schema::hasColumn('stock_taking_scan_logs', 'store_location_id')) {
                    $table->unsignedBigInteger('store_location_id')->nullable()->after('stock_taking_item_id')->index();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('stock_taking_items')) {
            Schema::table('stock_taking_items', function (Blueprint $table) {
                foreach (['store_location_id', 'system_store_location_id'] as $col) {
                    if (Schema::hasColumn('stock_taking_items', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('stock_taking_scan_logs')) {
            Schema::table('stock_taking_scan_logs', function (Blueprint $table) {
                if (Schema::hasColumn('stock_taking_scan_logs', 'store_location_id')) {
                    $table->dropColumn('store_location_id');
                }
            });
        }
    }
};

