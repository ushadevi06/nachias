<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_entry_items', function (Blueprint $table) {
            $table->unsignedBigInteger('fabric_width_id')->nullable()->after('brand_id');
            
            $table->foreign('fabric_width_id')->references('id')->on('fabric_sizes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_entry_items', function (Blueprint $table) {
            $table->dropForeign(['fabric_width_id']);
            $table->dropColumn('fabric_width_id');
        });
    }
};
