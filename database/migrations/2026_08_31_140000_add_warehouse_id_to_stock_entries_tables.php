<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('stock_entries', 'warehouse_id')) {
            Schema::table('stock_entries', function (Blueprint $table) {
                $table->foreignId('warehouse_id')->nullable()->after('grn_entry_id')->constrained('warehouses')->onDelete('set null');
            });
        }

        if (!Schema::hasColumn('stock_entry_items', 'warehouse_id')) {
            Schema::table('stock_entry_items', function (Blueprint $table) {
                $table->foreignId('warehouse_id')->nullable()->after('store_location_id')->constrained('warehouses')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('stock_entry_items', 'warehouse_id')) {
            Schema::table('stock_entry_items', function (Blueprint $table) {
                $table->dropForeign(['warehouse_id']);
                $table->dropColumn('warehouse_id');
            });
        }

        if (Schema::hasColumn('stock_entries', 'warehouse_id')) {
            Schema::table('stock_entries', function (Blueprint $table) {
                $table->dropForeign(['warehouse_id']);
                $table->dropColumn('warehouse_id');
            });
        }
    }
};
