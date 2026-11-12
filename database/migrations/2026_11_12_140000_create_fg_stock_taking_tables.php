<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('stock_taking_sessions')) {
            Schema::create('stock_taking_sessions', function (Blueprint $table) {
                $table->id();
                $table->string('session_no', 50)->unique();
                $table->unsignedBigInteger('brand_id')->index();
                $table->unsignedBigInteger('warehouse_id')->nullable()->index();
                $table->unsignedBigInteger('store_location_id')->nullable()->index();
                $table->string('title', 191)->nullable();
                $table->text('remarks')->nullable();
                $table->boolean('is_blind')->default(false);
                $table->string('status', 30)->default('In Progress')->index();
                $table->unsignedInteger('total_system_skus')->default(0);
                $table->decimal('total_system_qty', 14, 2)->default(0);
                $table->decimal('total_scanned_qty', 14, 2)->default(0);
                $table->unsignedBigInteger('started_by')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->unsignedBigInteger('completed_by')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('stock_taking_items')) {
            Schema::create('stock_taking_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('session_id')->index();
                $table->string('sku', 150)->index();
                $table->unsignedBigInteger('style_id')->nullable();
                $table->string('style_name', 150)->nullable();
                $table->string('art_no', 150)->nullable()->index();
                $table->string('finished_item_code', 150)->nullable();
                $table->string('size', 60)->nullable();
                $table->string('sleeve_type', 60)->nullable();
                $table->decimal('system_qty', 14, 2)->default(0);
                $table->decimal('scanned_qty', 14, 2)->default(0);
                $table->decimal('rate', 14, 2)->default(0);
                $table->timestamp('last_scanned_at')->nullable();
                $table->timestamps();

                $table->unique(['session_id', 'sku'], 'st_items_session_sku_unique');
            });
        }

        if (!Schema::hasTable('stock_taking_scan_logs')) {
            Schema::create('stock_taking_scan_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('session_id')->index();
                $table->unsignedBigInteger('stock_taking_item_id')->nullable()->index();
                $table->text('raw_code');
                $table->string('resolved_sku', 150)->nullable()->index();
                $table->unsignedBigInteger('detected_brand_id')->nullable();
                $table->string('detected_brand_name', 150)->nullable();
                $table->string('art_no', 150)->nullable();
                $table->string('size', 60)->nullable();
                $table->string('sleeve_type', 60)->nullable();
                $table->decimal('qty', 14, 2)->default(1);
                $table->string('scan_status', 30)->index();
                $table->string('message', 255)->nullable();
                $table->unsignedBigInteger('scanned_by')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_taking_scan_logs');
        Schema::dropIfExists('stock_taking_items');
        Schema::dropIfExists('stock_taking_sessions');
    }
};

