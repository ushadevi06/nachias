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
        Schema::create('production_stage_actual_consumables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_card_id');
            $table->unsignedBigInteger('production_id');
            $table->unsignedBigInteger('production_stage_id'); 
            $table->unsignedBigInteger('material_id');
            $table->decimal('planned_qty', 12, 4)->default(0);
            $table->decimal('actual_qty', 12, 4)->default(0);
            $table->string('uom')->nullable();
            $table->string('status')->default('Draft');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_stage_actual_consumables');
    }
};
