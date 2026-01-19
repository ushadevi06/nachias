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
        Schema::create('job_card_issue_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_card_entry_id')->constrained('job_card_entries')->onDelete('cascade');
            $table->foreignId('job_card_article_matrix_id')->constrained('job_card_article_matrices')->onDelete('cascade');
            $table->decimal('qty_issue', 15, 2)->default(0);
            $table->decimal('qty_adjusted', 15, 2)->default(0);
            $table->decimal('qty_wastage', 15, 2)->default(0);
            $table->decimal('qty_used', 15, 2)->default(0);
            $table->decimal('bit', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);
            $table->decimal('average', 15, 2)->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_card_issue_items');
    }
};
