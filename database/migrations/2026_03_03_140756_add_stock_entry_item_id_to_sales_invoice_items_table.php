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
            $table->unsignedBigInteger('stock_entry_item_id')->nullable()->after('sleeve_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_invoice_items', function (Blueprint $table) {
            $table->dropColumn('stock_entry_item_id');
        });
    }
};
