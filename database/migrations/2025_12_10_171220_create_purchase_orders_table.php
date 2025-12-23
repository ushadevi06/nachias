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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique();
            $table->date('po_date');
            $table->unsignedBigInteger('sales_agent_id')->nullable();
            $table->decimal('commission', 5, 2)->default(0);
            $table->unsignedBigInteger('supplier_id');
            $table->string('reference_no')->nullable();
            $table->date('reference_date')->nullable();
            $table->date('due_date');
            $table->unsignedBigInteger('store_type_id');
            $table->date('order_date');
            $table->text('payment_terms')->nullable();
            $table->enum('status', ['Draft', 'Approved', 'Dispatched', 'Received'])->default('Draft');
            $table->decimal('total_qty', 15, 2)->default(0);
            $table->decimal('sub_total', 15, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('taxable_amount', 15, 2)->default(0);
            $table->boolean('other_state')->default(false);
            $table->decimal('igst_percent', 5, 2)->default(0);
            $table->decimal('cgst_percent', 5, 2)->default(0);
            $table->decimal('sgst_percent', 5, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
