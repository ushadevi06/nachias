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
        Schema::table('debit_notes', function (Blueprint $table) {
            $table->decimal('discount_percent', 5, 2)->default(0.00)->after('sub_total');
            $table->decimal('discount_amount', 15, 2)->default(0.00)->after('discount_percent');
            $table->decimal('taxable_amount', 15, 2)->default(0.00)->after('discount_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('debit_notes', function (Blueprint $table) {
            $table->dropColumn(['discount_percent', 'discount_amount', 'taxable_amount']);
        });
    }
};
