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
        if (!Schema::hasTable('sales_invoices') || Schema::hasColumn('sales_invoices', 'no_of_box')) {
            return;
        }

        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->unsignedInteger('no_of_box')->nullable()->after('lr_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('sales_invoices') || !Schema::hasColumn('sales_invoices', 'no_of_box')) {
            return;
        }

        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->dropColumn('no_of_box');
        });
    }
};
