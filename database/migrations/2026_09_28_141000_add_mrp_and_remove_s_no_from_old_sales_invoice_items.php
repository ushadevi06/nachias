<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('old_sales_invoice_items', function (Blueprint $table) {
            if (Schema::hasColumn('old_sales_invoice_items', 's_no')) {
                $table->dropColumn('s_no');
            }
            if (!Schema::hasColumn('old_sales_invoice_items', 'mrp')) {
                $table->decimal('mrp', 15, 2)->default(0)->after('hsn_sac');
            }
        });
    }

    public function down(): void
    {
        Schema::table('old_sales_invoice_items', function (Blueprint $table) {
            if (!Schema::hasColumn('old_sales_invoice_items', 's_no')) {
                $table->integer('s_no')->nullable()->after('doc_no');
            }
            if (Schema::hasColumn('old_sales_invoice_items', 'mrp')) {
                $table->dropColumn('mrp');
            }
        });
    }
};
