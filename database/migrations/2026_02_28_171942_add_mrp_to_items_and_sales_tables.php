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
        Schema::table('items', function (Blueprint $table) {
            $table->decimal('mrp', 15, 2)->after('export_price')->nullable();
        });

        Schema::table('sale_order_items', function (Blueprint $table) {
            $table->decimal('mrp', 15, 2)->after('rate')->nullable();
        });

        Schema::table('sales_invoice_items', function (Blueprint $table) {
            $table->decimal('mrp', 15, 2)->after('rate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('mrp');
        });

        Schema::table('sale_order_items', function (Blueprint $table) {
            $table->dropColumn('mrp');
        });

        Schema::table('sales_invoice_items', function (Blueprint $table) {
            $table->dropColumn('mrp');
        });
    }
};
