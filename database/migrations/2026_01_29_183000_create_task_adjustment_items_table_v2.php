<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create the Detail Table
        if (!Schema::hasTable('task_adjustment_items')) {
            Schema::create('task_adjustment_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('task_adjustment_id');
                $table->unsignedBigInteger('raw_material_id');
                $table->string('material_category')->nullable();
                $table->string('adjustment_type');
                $table->decimal('qty', 15, 2);
                $table->unsignedBigInteger('uom_id')->nullable();
                $table->unsignedBigInteger('store_id')->nullable();
                $table->text('remarks')->nullable();
                $table->decimal('previous_stock', 15, 2)->default(0);
                $table->decimal('new_stock', 15, 2)->default(0);
                $table->timestamps();

                $table->foreign('task_adjustment_id')->references('id')->on('task_adjustments')->onDelete('cascade');
                $table->foreign('raw_material_id')->references('id')->on('raw_materials');
                $table->foreign('uom_id')->references('id')->on('uoms');
            });
        }

        // 2. Update the Header Table
        Schema::table('task_adjustments', function (Blueprint $table) {
            if (!Schema::hasColumn('task_adjustments', 'job_card_id')) {
                $table->unsignedBigInteger('job_card_id')->nullable()->after('task_id');
                $table->foreign('job_card_id')->references('id')->on('job_card_entries');
            }
            if (!Schema::hasColumn('task_adjustments', 'affected_stage')) {
                $table->string('affected_stage')->nullable()->after('job_card_id');
            }
            if (Schema::hasColumn('task_adjustments', 'reason') && !Schema::hasColumn('task_adjustments', 'overall_reason')) {
                $table->text('overall_reason')->nullable()->after('reason');
            }
        });

        // 3. Data Migration
        try {
            DB::statement("UPDATE task_adjustments SET overall_reason = reason WHERE overall_reason IS NULL AND reason IS NOT NULL");
        } catch (\Exception $e) {
            // Log or ignore if table is empty/new
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('task_adjustment_items');
        Schema::table('task_adjustments', function (Blueprint $table) {
            if (Schema::hasColumn('task_adjustments', 'overall_reason')) {
                $table->dropColumn('overall_reason');
            }
            if (Schema::hasColumn('task_adjustments', 'job_card_id')) {
                $table->dropForeign(['job_card_id']);
                $table->dropColumn('job_card_id');
            }
            if (Schema::hasColumn('task_adjustments', 'affected_stage')) {
                $table->dropColumn('affected_stage');
            }
        });
    }
};
