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
            if (!Schema::hasColumn('sales_invoices', 'is_gst_filed')) {
                $table->boolean('is_gst_filed')->default(0)->after('einvoice_status');
            }
            if (!Schema::hasColumn('sales_invoices', 'gst_filed_at')) {
                $table->timestamp('gst_filed_at')->nullable()->after('is_gst_filed');
            }
            if (!Schema::hasColumn('sales_invoices', 'gst_filed_by')) {
                $table->unsignedBigInteger('gst_filed_by')->nullable()->after('gst_filed_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            if (Schema::hasColumn('sales_invoices', 'gst_filed_by')) {
                $table->dropColumn('gst_filed_by');
            }
            if (Schema::hasColumn('sales_invoices', 'gst_filed_at')) {
                $table->dropColumn('gst_filed_at');
            }
            if (Schema::hasColumn('sales_invoices', 'is_gst_filed')) {
                $table->dropColumn('is_gst_filed');
            }
        });
    }
};
