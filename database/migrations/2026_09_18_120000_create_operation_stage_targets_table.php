<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('operation_stage_targets')) {
            Schema::create('operation_stage_targets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('operation_stage_id');
                $table->unsignedBigInteger('service_provider_id');
                $table->integer('target_qty')->default(0);
                $table->timestamps();

                $table->unique(['operation_stage_id', 'service_provider_id'], 'stage_provider_target_unique');
                $table->index('operation_stage_id');
                $table->index('service_provider_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_stage_targets');
    }
};
