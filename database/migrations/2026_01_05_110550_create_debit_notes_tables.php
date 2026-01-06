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
        Schema::create('debit_notes', function (Blueprint $table) {
            $table->id();
            $table->string('debit_note_no')->unique();
            $table->date('debit_note_date');
            $table->unsignedBigInteger('purchase_invoice_id');
            $table->unsignedBigInteger('supplier_id');
            $table->string('reason')->nullable();
            $table->decimal('sub_total', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('other_charges', 15, 2)->default(0);
            $table->enum('round_off_type', ['Add', 'Less'])->default('Add');
            $table->decimal('round_off', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->string('reference_document')->nullable();
            $table->string('status')->default('Active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            /*
            $table->foreign('purchase_invoice_id')->references('id')->on('purchase_invoices');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            */
        });

        Schema::create('debit_note_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('debit_note_id');
            $table->unsignedBigInteger('purchase_invoice_item_id');
            $table->unsignedBigInteger('raw_material_id');
            $table->decimal('quantity', 15, 2);
            $table->unsignedBigInteger('uom_id');
            $table->decimal('rate', 15, 2);
            $table->decimal('amount', 15, 2);
            $table->softDeletes();
            $table->timestamps();

            /*
            $table->foreign('debit_note_id')->references('id')->on('debit_notes')->onDelete('cascade');
            $table->foreign('purchase_invoice_item_id')->references('id')->on('purchase_invoice_items');
            $table->foreign('raw_material_id')->references('id')->on('raw_materials');
            $table->foreign('uom_id')->references('id')->on('uoms');
            */
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debit_note_items');
        Schema::dropIfExists('debit_notes');
    }
};
