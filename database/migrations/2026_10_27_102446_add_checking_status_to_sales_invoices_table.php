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
            $table->string('checking_status', 50)->default('pending')->after('delivery_status');
            $table->unsignedBigInteger('checked_by')->nullable()->after('checking_status');
            $table->dateTime('checked_at')->nullable()->after('checked_by');

            $table->index('checking_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->dropIndex(['checking_status']);
            $table->dropColumn(['checking_status', 'checked_by', 'checked_at']);
        });
    }
};
