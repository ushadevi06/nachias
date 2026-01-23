<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_receipt_id');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_code')->nullable();
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->string('size_variant')->nullable();
            $table->unsignedBigInteger('uom_id')->nullable();
            $table->string('uom_code')->nullable();
            $table->decimal('ordered_qty', 15, 2)->default(0);
            $table->decimal('completed_qty', 15, 2)->default(0);
            $table->decimal('qty_already_received', 15, 2)->default(0);
            $table->decimal('qty_to_receive', 15, 2)->default(0);
            $table->decimal('balance_qty', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('production_receipt_id')->references('id')->on('production_receipts')->onDelete('cascade');
            $table->index('production_receipt_id');
            $table->index('item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_receipt_items');
    }
};
