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
        Schema::create('credit_note_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('credit_note_id');
            $table->unsignedBigInteger('sales_invoice_item_id')->nullable();
            $table->unsignedBigInteger('item_id');
            $table->decimal('quantity', 15, 2);
            $table->unsignedBigInteger('uom_id')->nullable();
            $table->decimal('rate', 15, 2);
            $table->decimal('amount', 15, 2);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('credit_note_id')->references('id')->on('credit_notes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_note_items');
    }
};
