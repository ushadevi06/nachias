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
        Schema::table('sales_invoice_items', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_invoice_items', 'stock_allocations')) {
                $table->json('stock_allocations')->nullable()->after('stock_entry_item_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_invoice_items', function (Blueprint $table) {
            if (Schema::hasColumn('sales_invoice_items', 'stock_allocations')) {
                $table->dropColumn('stock_allocations');
            }
        });
    }
};
