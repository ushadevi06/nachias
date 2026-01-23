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
        Schema::table('production_receipts', function (Blueprint $table) {
            if (!Schema::hasColumn('production_receipts', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('job_card_id');
            }
            if (!Schema::hasColumn('production_receipts', 'order_due_date')) {
                $table->date('order_due_date')->nullable()->after('customer_name');
            }
        });

        Schema::table('production_receipt_items', function (Blueprint $table) {
            if (!Schema::hasColumn('production_receipt_items', 'art_no')) {
                $table->string('art_no')->nullable()->after('item_name');
            }
            if (!Schema::hasColumn('production_receipt_items', 'size')) {
                $table->string('size')->nullable()->after('art_no');
            }
            if (!Schema::hasColumn('production_receipt_items', 'unit_price')) {
                $table->decimal('unit_price', 15, 2)->default(0)->after('size');
            }
            if (!Schema::hasColumn('production_receipt_items', 'total_value')) {
                $table->decimal('total_value', 15, 2)->default(0)->after('unit_price');
            }
            if (!Schema::hasColumn('production_receipt_items', 'scan_qty')) {
                $table->decimal('scan_qty', 15, 2)->default(0)->after('qty_already_received');
            }
            if (!Schema::hasColumn('production_receipt_items', 'damage_qty')) {
                $table->decimal('damage_qty', 15, 2)->default(0)->after('scan_qty');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_receipts', function (Blueprint $table) {
            $table->dropColumn(['customer_name', 'order_due_date']);
        });

        Schema::table('production_receipt_items', function (Blueprint $table) {
            $table->dropColumn(['art_no', 'size', 'unit_price', 'total_value', 'scan_qty', 'damage_qty']);
        });
    }
};
