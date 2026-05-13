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
            $table->string('tran_doc_no')->nullable()->after('transporter_name');
            $table->string('tran_doc_date')->nullable()->after('tran_doc_no');
            $table->string('veh_type')->nullable()->after('tran_doc_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->dropColumn([
                'tran_doc_no',
                'tran_doc_date',
                'veh_type',
            ]);
        });
    }
};
