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
        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('inv_no', 100)->unique();
            $table->date('inv_date');
            $table->unsignedBigInteger('so_id')->nullable();
            $table->unsignedBigInteger('customer_id');
            $table->text('delivery_address')->nullable();
            $table->text('remarks')->nullable();
            $table->string('invoice_status', 50)->nullable();
            $table->string('payment_mode', 50)->nullable();
            $table->string('extra_input', 100)->nullable();
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('signature_file')->nullable();
            $table->string('attachment_file')->nullable();
            $table->text('show_fields')->nullable();
            $table->decimal('sub_total', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->boolean('other_state')->default(false);
            $table->decimal('igst', 15, 2)->default(0);
            $table->decimal('cgst', 15, 2)->default(0);
            $table->decimal('sgst', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->decimal('received_amount', 15, 2)->default(0);
            $table->decimal('due_amount', 15, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_invoices');
    }
};
