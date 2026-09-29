<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('old_sales_invoice_items', function (Blueprint $table) {
            if (Schema::hasColumn('old_sales_invoice_items', 'discount_amount')) {
                $table->dropColumn('discount_amount');
            }
            if (Schema::hasColumn('old_sales_invoice_items', 'taxable_amount')) {
                $table->dropColumn('taxable_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('old_sales_invoice_items', function (Blueprint $table) {
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('taxable_amount', 15, 2)->default(0);
        });
    }
};
