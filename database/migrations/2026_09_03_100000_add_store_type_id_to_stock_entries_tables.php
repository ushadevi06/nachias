<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('stock_entries', 'store_type_id')) {
            Schema::table('stock_entries', function (Blueprint $table) {
                $table->unsignedBigInteger('store_type_id')->nullable()->after('warehouse_id');
                $table->index('store_type_id');
            });
        }

        if (!Schema::hasColumn('stock_entry_items', 'store_type_id')) {
            Schema::table('stock_entry_items', function (Blueprint $table) {
                $table->unsignedBigInteger('store_type_id')->nullable()->after('store_location_id');
                $table->index('store_type_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('stock_entry_items', 'store_type_id')) {
            Schema::table('stock_entry_items', function (Blueprint $table) {
                $table->dropIndex(['store_type_id']);
                $table->dropColumn('store_type_id');
            });
        }

        if (Schema::hasColumn('stock_entries', 'store_type_id')) {
            Schema::table('stock_entries', function (Blueprint $table) {
                $table->dropIndex(['store_type_id']);
                $table->dropColumn('store_type_id');
            });
        }
    }
};
