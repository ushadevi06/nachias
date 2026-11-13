<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('open_stock_entry_logs')) {
            Schema::create('open_stock_entry_logs', function (Blueprint $table) {
                $table->id();
                $table->string('entry_no', 50)->index();
                $table->date('entry_date')->index();
                $table->unsignedBigInteger('warehouse_id')->nullable()->index();
                $table->unsignedBigInteger('store_type_id')->nullable()->index();
                $table->unsignedBigInteger('brand_id')->nullable()->index();
                $table->string('product_name', 255);
                $table->string('art_no', 100)->index();
                $table->unsignedBigInteger('style_id')->nullable()->index();
                $table->string('size', 50);
                $table->unsignedBigInteger('color_id')->nullable();
                $table->string('sleeve_type', 20)->nullable();
                $table->decimal('qty', 10, 2)->default(0);
                $table->decimal('price', 12, 2)->nullable();
                $table->unsignedBigInteger('store_location_id')->nullable()->index();
                $table->string('sku', 100)->index();
                $table->unsignedBigInteger('uom_id')->nullable();
                $table->string('remarks', 255)->nullable();
                $table->string('header_remarks', 500)->nullable();
                $table->string('status', 50)->default('Logged')->index();
                $table->unsignedBigInteger('created_by')->nullable()->index();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('open_stock_entry_logs');
    }
};

