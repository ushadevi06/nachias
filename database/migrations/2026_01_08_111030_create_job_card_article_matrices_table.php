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
        Schema::create('job_card_article_matrices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_card_entry_id');
            $table->string('art_no')->nullable();
            $table->integer('fs_36')->default(0);
            $table->integer('fs_38')->default(0);
            $table->integer('fs_40')->default(0);
            $table->integer('fs_42')->default(0);
            $table->integer('fs_44')->default(0);
            $table->integer('fs_46')->default(0);
            $table->integer('hs_38')->default(0);
            $table->integer('hs_40')->default(0);
            $table->integer('hs_42')->default(0);
            $table->integer('hs_44')->default(0);
            $table->integer('ex_1')->default(0); // 40 H/S
            $table->integer('ex_2')->default(0); // 38 F/S
            $table->integer('row_total')->default(0);
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
        Schema::dropIfExists('job_card_article_matrices');
    }
};
