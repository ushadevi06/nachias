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
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->decimal('discount_percent', 10, 2)->default(0)->after('sub_total');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('sgst');
            $table->decimal('igst_percent', 10, 2)->default(0)->after('tax_amount');
            $table->decimal('cgst_percent', 10, 2)->default(0)->after('igst_percent');
            $table->decimal('sgst_percent', 10, 2)->default(0)->after('cgst_percent');
            $table->decimal('other_charges', 10, 2)->default(0)->after('sgst_percent');
            $table->string('round_off_type', 10)->default('add')->after('other_charges'); // add, less
            $table->decimal('round_off_amount', 10, 2)->default(0)->after('round_off_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->dropColumn([
                'discount_percent', 'tax_amount', 'igst_percent', 
                'cgst_percent', 'sgst_percent', 'other_charges', 
                'round_off_type', 'round_off_amount'
            ]);
        });
    }
};
