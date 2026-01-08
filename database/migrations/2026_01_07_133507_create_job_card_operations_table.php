<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_card_operations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_card_entry_id');
            $table->unsignedBigInteger('operation_stage_id');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->date('assigned_date')->nullable();
            $table->timestamps();

            $table->foreign('job_card_entry_id')->references('id')->on('job_card_entries')->onDelete('cascade');
            $table->foreign('operation_stage_id')->references('id')->on('operation_stages')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_card_operations');
    }
};
