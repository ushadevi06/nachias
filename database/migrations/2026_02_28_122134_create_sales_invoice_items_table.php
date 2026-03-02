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
        Schema::create('sales_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_invoice_id');
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('uom_id')->nullable();
            $table->decimal('quantity', 15, 2)->default(0);
            $table->decimal('rate', 15, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('hsn_sac', 50)->nullable();
            $table->string('art_no', 100)->nullable();
            $table->string('size', 50)->nullable();
            $table->string('sleeve_type', 50)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('sales_invoice_id')->references('id')->on('sales_invoices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_invoice_items');
    }
};
