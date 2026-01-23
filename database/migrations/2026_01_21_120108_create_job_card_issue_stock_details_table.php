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
        Schema::create('job_card_issue_stock_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_card_issue_item_id')->constrained('job_card_issue_items')->onDelete('cascade');
            $table->foreignId('stock_entry_item_id')->constrained('stock_entry_items')->onDelete('cascade');
            $table->decimal('qty', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_card_issue_stock_details');
    }
};
