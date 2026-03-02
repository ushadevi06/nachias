<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_order_id');
            $table->unsignedBigInteger('brand_cat_id')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->unsignedBigInteger('color_id')->nullable();
            $table->string('art_no',50)->nullable();
            $table->unsignedBigInteger('uom_id')->nullable();
            $table->unsignedBigInteger('size_id')->nullable();
            $table->decimal('qty', 15, 2)->default(0);
            $table->decimal('rate', 15, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->json('sleeve')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('sale_order_id')->references('id')->on('sale_orders')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_order_items');
    }
};
