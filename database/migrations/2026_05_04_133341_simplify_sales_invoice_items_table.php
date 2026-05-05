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
            $table->dropColumn(['brand_id', 'item_id']);
            $table->string('sku')->nullable()->after('sales_invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_invoice_items', function (Blueprint $table) {
            $table->unsignedBigInteger('brand_id')->nullable()->after('sales_invoice_id');
            $table->unsignedBigInteger('item_id')->nullable()->after('brand_id');
            $table->dropColumn('sku');
        });
    }
};
