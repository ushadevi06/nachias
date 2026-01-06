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
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('additional_attachments')->nullable()->after('status');
            $table->enum('round_off_type', ['Add', 'Less'])->nullable()->after('tax_amount');
            $table->decimal('round_off', 10, 2)->default(0)->after('round_off_type');
        });

        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->unsignedBigInteger('color_id')->nullable()->after('uom_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['additional_attachments', 'round_off_type', 'round_off']);
        });

        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropColumn('color_id');
        });
    }
};
