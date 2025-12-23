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
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_id');
            $table->unsignedBigInteger('store_category_id');
            $table->unsignedBigInteger('raw_material_id');
            $table->unsignedBigInteger('uom_id');
            $table->decimal('quantity', 15, 2);
            $table->string('art_no')->nullable();
            $table->decimal('rate', 15, 2);
            $table->decimal('amount', 15, 2);
            $table->text('remarks')->nullable();
            $table->string('attached_file')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
