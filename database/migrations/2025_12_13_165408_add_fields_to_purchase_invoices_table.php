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
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->decimal('cgst_amount', 15, 2)->nullable()->after('cgst_percent');
            $table->decimal('sgst_amount', 15, 2)->nullable()->after('sgst_percent');
            $table->decimal('igst_amount', 15, 2)->nullable()->after('igst_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $table) {
            //
        });
    }
};
