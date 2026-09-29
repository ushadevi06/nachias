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
        if (Schema::hasColumn('brands', 'store_category_id')) {
            Schema::table('brands', function (Blueprint $table) {
                // Drop foreign key first if it exists
                $table->dropForeign(['store_category_id']);
                $table->dropColumn('store_category_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('brands', 'store_category_id')) {
            Schema::table('brands', function (Blueprint $table) {
                $table->unsignedBigInteger('store_category_id')->nullable()->after('code');
                $table->foreign('store_category_id')->references('id')->on('store_categories')->nullOnDelete();
            });
        }
    }
};
