<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('old_sales_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('doc_no')->index();
            $table->date('doc_date')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('gstin_reg_no')->nullable();
            $table->string('vehicle_no')->nullable();
            $table->string('pincode')->nullable();
            $table->text('bill_to')->nullable();
            $table->text('ship_to')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->text('irn_no')->nullable();
            $table->string('ack_no')->nullable();
            $table->string('ack_date')->nullable();
            $table->decimal('total_qty', 15, 2)->default(0);
            $table->decimal('sub_total', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('taxable_amount', 15, 2)->default(0);
            $table->decimal('cgst_amount', 15, 2)->default(0);
            $table->decimal('sgst_amount', 15, 2)->default(0);
            $table->decimal('igst_amount', 15, 2)->default(0);
            $table->decimal('round_off', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('old_sales_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('old_sales_invoice_id')->constrained('old_sales_invoices')->onDelete('cascade');
            $table->string('doc_no')->nullable();
            $table->string('description')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('style_id')->nullable();
            $table->string('size')->nullable();
            $table->string('class2_sleeve')->nullable();
            $table->string('hsn_sac')->nullable();
            $table->decimal('mrp', 15, 2)->default(0);
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('quantity', 15, 2)->default(0);
            $table->decimal('gross_amount', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('old_sales_invoice_items');
        Schema::dropIfExists('old_sales_invoices');
    }
};
