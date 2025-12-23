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
        Schema::table('purchase_invoice_items', function (Blueprint $table) {
            $table->decimal('qty_ordered', 15, 2)->default(0.00)->after('amount');
            $table->decimal('qty_received', 15, 2)->default(0.00)->after('qty_ordered');
            $table->decimal('qty_invoiced', 15, 2)->default(0.00)->after('qty_received');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
