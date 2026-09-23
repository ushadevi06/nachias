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
        Schema::table('credit_note_items', function (Blueprint $table) {
            $table->unsignedBigInteger('stock_entry_item_id')->nullable()->after('sales_invoice_item_id');
            $table->string('art_no')->nullable()->after('size');
            $table->unsignedBigInteger('color_id')->nullable()->after('art_no');
            $table->string('sku')->nullable()->after('color_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_note_items', function (Blueprint $table) {
            $table->dropColumn(['stock_entry_item_id', 'art_no', 'color_id', 'sku']);
        });
    }
};
