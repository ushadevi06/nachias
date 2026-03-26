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
        Schema::table('stock_entry_items', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_entry_items', 'item_id')) {
                $table->unsignedBigInteger('item_id')->nullable()->after('raw_material_id');
                $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_entry_items', function (Blueprint $table) {
            if (Schema::hasColumn('stock_entry_items', 'item_id')) {
                $table->dropForeign(['item_id']);
                $table->dropColumn('item_id');
            }
        });
    }
};
