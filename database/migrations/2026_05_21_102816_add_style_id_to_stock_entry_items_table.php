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
            $table->unsignedBigInteger('style_id')->nullable()->after('fabric_type_id');
            
            $table->foreign('style_id')->references('id')->on('styles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_entry_items', function (Blueprint $table) {
            $table->dropForeign(['style_id']);
            $table->dropColumn('style_id');
        });
    }
};
