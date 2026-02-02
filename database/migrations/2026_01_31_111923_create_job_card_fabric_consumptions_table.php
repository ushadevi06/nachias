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
        Schema::create('job_card_fabric_consumptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_card_fabric_detail_id')->constrained('job_card_fabric_details')->onDelete('cascade');
            $table->string('size');
            $table->decimal('fs_cons', 10, 3)->nullable();
            $table->decimal('hs_cons', 10, 3)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_card_fabric_consumptions');
    }
};
