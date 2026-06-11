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
            $table->decimal('total_qty', 10, 3)->nullable()->after('job_card_article_matrix_id')
                  ->comment('Total available stock qty at the time this job card was created');
        });
 
        Schema::table('job_card_fabric_details', function (Blueprint $table) {
            $table->decimal('stock_total_qty', 10, 3)->nullable()->after('mtr')
                  ->comment('Snapshot of total stock qty_in at the time of job card save');
        });
    }
 
    public function down(): void
    {
        Schema::table('job_card_issue_items', function (Blueprint $table) {
            $table->dropColumn('total_qty');
        });
 
        Schema::table('job_card_fabric_details', function (Blueprint $table) {
            $table->dropColumn('stock_total_qty');
        });
    }
};
