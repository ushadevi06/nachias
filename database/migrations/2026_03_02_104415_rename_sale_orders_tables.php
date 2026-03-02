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
        Schema::rename('sale_orders', 'sales_orders');
        Schema::rename('sale_order_items', 'sales_order_items');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('sales_orders', 'sale_orders');
        Schema::rename('sales_order_items', 'sale_order_items');
    }
};
