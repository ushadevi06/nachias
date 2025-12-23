<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_category_id');
            $table->string('name', 150);
            $table->string('supplier_design_name', 150)->nullable();
            $table->unsignedBigInteger('color_id');
            $table->string('size_width', 100)->nullable();
            $table->unsignedBigInteger('uom_id');
            $table->unsignedBigInteger('fabric_type_id');
            $table->string('reference_image')->nullable();
            $table->string('specification', 255)->nullable();
            $table->integer('min_stock')->default(0);
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->unsignedBigInteger('created_by');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raw_materials');
    }
};
