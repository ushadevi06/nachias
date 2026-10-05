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
        $existingIndexes = collect(DB::select("SHOW INDEX FROM sales_invoices"))->pluck('Key_name')->unique()->toArray();

        Schema::table('sales_invoices', function (Blueprint $table) use ($existingIndexes) {
            if (!in_array('sales_invoices_customer_id_index', $existingIndexes)) {
                $table->index('customer_id', 'sales_invoices_customer_id_index');
            }
            if (!in_array('sales_invoices_inv_date_index', $existingIndexes)) {
                $table->index('inv_date', 'sales_invoices_inv_date_index');
            }
            if (!in_array('sales_invoices_invoice_status_index', $existingIndexes)) {
                $table->index('invoice_status', 'sales_invoices_invoice_status_index');
            }
            if (!in_array('sales_invoices_delivery_status_index', $existingIndexes)) {
                $table->index('delivery_status', 'sales_invoices_delivery_status_index');
            }
            if (!in_array('sales_invoices_so_id_index', $existingIndexes)) {
                $table->index('so_id', 'sales_invoices_so_id_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            $existingIndexes = collect(DB::select("SHOW INDEX FROM sales_invoices"))->pluck('Key_name')->unique()->toArray();
            
            if (in_array('sales_invoices_customer_id_index', $existingIndexes)) {
                $table->dropIndex('sales_invoices_customer_id_index');
            }
            if (in_array('sales_invoices_inv_date_index', $existingIndexes)) {
                $table->dropIndex('sales_invoices_inv_date_index');
            }
            if (in_array('sales_invoices_invoice_status_index', $existingIndexes)) {
                $table->dropIndex('sales_invoices_invoice_status_index');
            }
            if (in_array('sales_invoices_delivery_status_index', $existingIndexes)) {
                $table->dropIndex('sales_invoices_delivery_status_index');
            }
            if (in_array('sales_invoices_so_id_index', $existingIndexes)) {
                $table->dropIndex('sales_invoices_so_id_index');
            }
        });
    }
};
