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
        Schema::create('barcode_masters', function (Blueprint $table) {
            $table->id();
            $table->string('barcode_no')->unique();
            $table->string('art_no')->nullable();
            $table->string('item_name')->nullable();
            $table->string('sleeve_type')->nullable();
            $table->string('size')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->string('lot_no')->nullable();
            $table->unsignedBigInteger('color_id')->nullable();
            $table->unsignedBigInteger('fabric_type_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barcode_masters');
    }
};
