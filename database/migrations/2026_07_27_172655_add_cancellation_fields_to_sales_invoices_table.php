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
            $table->dateTime('cancellation_date')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->string('cancel_remarks')->nullable();
            $table->unsignedBigInteger('cancelled_by')->nullable();
            $table->boolean('stock_reverted')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            //
        });
    }
};
