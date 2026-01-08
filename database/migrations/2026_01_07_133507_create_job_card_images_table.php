<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_card_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_card_entry_id');
            $table->string('image_path');
            $table->string('image_type')->comment('fabric, pattern, reference, etc.');
            $table->timestamps();

            $table->foreign('job_card_entry_id')->references('id')->on('job_card_entries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_card_images');
    }
};
