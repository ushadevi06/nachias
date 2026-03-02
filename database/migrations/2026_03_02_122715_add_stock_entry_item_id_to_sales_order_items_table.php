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
        Schema::table('sales_order_items', function (Blueprint $table) {
            $table->foreignId('stock_entry_item_id')->nullable()->after('item_id')->constrained('stock_entry_items');
        });

        Schema::table('sales_invoice_items', function (Blueprint $table) {
            $table->foreignId('stock_entry_item_id')->nullable()->after('item_id')->constrained('stock_entry_items');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_invoice_items', function (Blueprint $table) {
            $table->dropForeign(['stock_entry_item_id']);
            $table->dropColumn('stock_entry_item_id');
        });

        Schema::table('sales_order_items', function (Blueprint $table) {
            $table->dropForeign(['stock_entry_item_id']);
            $table->dropColumn('stock_entry_item_id');
        });
    }
};
