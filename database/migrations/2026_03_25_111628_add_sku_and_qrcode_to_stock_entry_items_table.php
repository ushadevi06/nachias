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
        Schema::table('stock_entry_items', function (Blueprint $table) {
            $table->string('sku')->nullable()->after('finished_item_code');
            $table->text('qrcode')->nullable()->after('sku');
            $table->string('fabric_type')->nullable()->after('qrcode');
            $table->string('sleeve_type')->nullable()->after('fabric_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_entry_items', function (Blueprint $table) {
            $table->dropColumn(['sku', 'qrcode', 'fabric_type', 'sleeve_type']);
        });
    }
};
