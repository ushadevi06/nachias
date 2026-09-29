<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('brand_store_categories')) {
            Schema::create('brand_store_categories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('brand_id');
                $table->unsignedBigInteger('store_category_id');
                $table->timestamps();

                $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
                $table->foreign('store_category_id')->references('id')->on('store_categories')->onDelete('cascade');
            });
        }

        // Migrate existing single store_category_id data if present
        if (Schema::hasColumn('brands', 'store_category_id')) {
            $existing = DB::table('brands')->whereNotNull('store_category_id')->get();
            foreach ($existing as $b) {
                DB::table('brand_store_categories')->updateOrInsert(
                    ['brand_id' => $b->id, 'store_category_id' => $b->store_category_id],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_store_categories');
    }
};
