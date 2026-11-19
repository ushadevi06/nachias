<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('stock_entry_items')) {
            Schema::table('stock_entry_items', function (Blueprint $table) {
                if (!Schema::hasColumn('stock_entry_items', 'excess_qty')) {
                    $table->decimal('excess_qty', 10, 2)->default(0)->after('qty_out');
                }
                if (!Schema::hasColumn('stock_entry_items', 'shortage_qty')) {
                    $table->decimal('shortage_qty', 10, 2)->default(0)->after('excess_qty');
                }
            });
        }

        if (Schema::hasTable('stock_taking_items')) {
            Schema::table('stock_taking_items', function (Blueprint $table) {
                if (!Schema::hasColumn('stock_taking_items', 'excess_qty')) {
                    $table->decimal('excess_qty', 10, 2)->default(0)->after('scanned_qty');
                }
                if (!Schema::hasColumn('stock_taking_items', 'shortage_qty')) {
                    $table->decimal('shortage_qty', 10, 2)->default(0)->after('excess_qty');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('stock_entry_items')) {
            Schema::table('stock_entry_items', function (Blueprint $table) {
                if (Schema::hasColumn('stock_entry_items', 'excess_qty')) {
                    $table->dropColumn('excess_qty');
                }
                if (Schema::hasColumn('stock_entry_items', 'shortage_qty')) {
                    $table->dropColumn('shortage_qty');
                }
            });
        }

        if (Schema::hasTable('stock_taking_items')) {
            Schema::table('stock_taking_items', function (Blueprint $table) {
                if (Schema::hasColumn('stock_taking_items', 'excess_qty')) {
                    $table->dropColumn('excess_qty');
                }
                if (Schema::hasColumn('stock_taking_items', 'shortage_qty')) {
                    $table->dropColumn('shortage_qty');
                }
            });
        }
    }
};
