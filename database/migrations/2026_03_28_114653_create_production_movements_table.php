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
        Schema::create('production_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_card_id');
            $table->unsignedBigInteger('process_schedule_id')->nullable();
            $table->unsignedBigInteger('operation_stage_id')->nullable();
            $table->unsignedBigInteger('production_service_id')->nullable();
            $table->unsignedBigInteger('task_id')->nullable();
            $table->decimal('inward_qty', 10, 2)->default(0);
            $table->decimal('outward_qty', 10, 2)->default(0);
            $table->decimal('wastage_qty', 10, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_movements');
    }
};
