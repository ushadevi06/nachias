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
        Schema::create('credit_notes', function (Blueprint $table) {
            $table->id();
            $table->string('note_no', 50)->unique();
            $table->date('note_date');
            $table->unsignedBigInteger('sales_invoice_id')->nullable();
            $table->unsignedBigInteger('customer_id');
            $table->string('reason')->nullable();
            $table->boolean('other_state')->default(false);
            $table->decimal('igst_percent', 5, 2)->default(0);
            $table->decimal('igst', 15, 2)->default(0);
            $table->decimal('cgst_percent', 5, 2)->default(0);
            $table->decimal('cgst', 15, 2)->default(0);
            $table->decimal('sgst_percent', 5, 2)->default(0);
            $table->decimal('sgst', 15, 2)->default(0);
            $table->decimal('sub_total', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->string('reference_doc')->nullable();
            $table->string('status')->default('Draft');
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
        Schema::dropIfExists('credit_notes');
    }
};
