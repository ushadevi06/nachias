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
        Schema::table('production_receipt_items', function (Blueprint $table) {
            if (Schema::hasColumn('production_receipt_items', 'color')) {
                $table->dropColumn('color');
            }
            if (!Schema::hasColumn('production_receipt_items', 'color_id')) {
                $table->unsignedBigInteger('color_id')->nullable()->after('size');
            }
        });

        Schema::table('stock_entry_items', function (Blueprint $table) {
            if (Schema::hasColumn('stock_entry_items', 'color')) {
                $table->dropColumn('color');
            }
            if (!Schema::hasColumn('stock_entry_items', 'color_id')) {
                $table->unsignedBigInteger('color_id')->nullable()->after('size');
            }
            if (Schema::hasColumn('stock_entry_items', 'fabric_type')) {
                $table->dropColumn('fabric_type');
            }
            if (!Schema::hasColumn('stock_entry_items', 'fabric_type_id')) {
                $table->unsignedBigInteger('fabric_type_id')->nullable()->after('qrcode');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_receipt_items', function (Blueprint $table) {
            $table->dropColumn(['color_id']);
        });

        Schema::table('stock_entry_items', function (Blueprint $table) {
            $table->dropColumn(['color_id', 'fabric_type_id']);
        });
    }
};
