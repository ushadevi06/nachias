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
        Schema::table('job_card_issue_items', function (Blueprint $table) {
            $table->unsignedBigInteger('raw_material_id')->nullable()->after('stock_entry_item_id');
            $table->foreign('raw_material_id')->references('id')->on('raw_materials');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_card_issue_items', function (Blueprint $table) {
            $table->dropForeign(['raw_material_id']);
            $table->dropColumn('raw_material_id');
        });
    }
};
