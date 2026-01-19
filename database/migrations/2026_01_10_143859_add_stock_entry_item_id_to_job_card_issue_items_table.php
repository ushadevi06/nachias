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
            $table->unsignedBigInteger('stock_entry_item_id')->nullable()->after('job_card_article_matrix_id');
            // Adding index for better performance
            $table->index('stock_entry_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_card_issue_items', function (Blueprint $table) {
            $table->dropColumn('stock_entry_item_id');
        });
    }
};
