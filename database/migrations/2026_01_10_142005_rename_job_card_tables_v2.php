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
        Schema::rename('job_card_items', 'job_card_cutting_size_ratios');
        Schema::rename('job_card_article_matrices', 'job_card_fabric_details');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('job_card_cutting_size_ratios', 'job_card_items');
        Schema::rename('job_card_fabric_details', 'job_card_article_matrices');
    }
};
