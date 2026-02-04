<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('production_stage_consumables', function (Blueprint $table) {
            $table->unsignedBigInteger('job_card_id')->nullable()->after('id');
            $table->unsignedBigInteger('production_id')->nullable()->after('job_card_id');
            $table->unsignedBigInteger('production_stage_id')->nullable()->after('production_id');
            $table->decimal('planned_qty', 12, 4)->nullable()->after('quantity_per_unit');
            $table->decimal('actual_qty', 12, 4)->nullable()->after('planned_qty');
        });
    }

    public function down(): void
    {
        Schema::table('production_stage_consumables', function (Blueprint $table) {
            $table->dropColumn(['job_card_id', 'production_id', 'production_stage_id', 'planned_qty', 'actual_qty']);
        });
    }
};
