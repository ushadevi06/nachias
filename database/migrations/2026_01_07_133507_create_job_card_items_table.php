<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_card_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_card_entry_id');
            $table->string('article_no')->nullable();
            $table->string('size')->nullable();
            $table->decimal('ratio', 10, 2)->default(0);
            $table->decimal('qty_fs', 15, 2)->default(0);
            $table->decimal('qty_hs', 15, 2)->default(0);
            $table->decimal('total_qty', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('job_card_entry_id')->references('id')->on('job_card_entries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_card_items');
    }
};
