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
        Schema::table('old_sales_invoices', function (Blueprint $table) {
            $table->decimal('courier_charges', 15, 2)->default(0)->after('igst_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('old_sales_invoices', function (Blueprint $table) {
            $table->dropColumn('courier_charges');
        });
    }
};
