<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('brand_category_id');
            $table->unsignedBigInteger('brand_id');
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->string('entry_type')->nullable();
            $table->string('style')->nullable();
            $table->unsignedBigInteger('fabric_type_id')->nullable();
            $table->string('design_art_no')->nullable();
            $table->unsignedBigInteger('uom_id');
            $table->unsignedBigInteger('size_ratio_id')->nullable();
            $table->unsignedBigInteger('color_id')->nullable();
            $table->json('colors')->nullable();
            $table->string('product_barcode')->unique();
            $table->decimal('standard_costing', 10, 2)->nullable();
            $table->unsignedBigInteger('store_category_id')->nullable();
            $table->json('related_materials')->nullable();
            $table->json('operation_stages')->nullable();
            $table->json('service_providers')->nullable();
            $table->decimal('wholesale_price', 10, 2)->nullable();
            $table->decimal('retail_price', 10, 2)->nullable();
            $table->decimal('export_price', 10, 2)->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
