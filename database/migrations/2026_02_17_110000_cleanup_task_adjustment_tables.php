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
        Schema::table('task_adjustments', function (Blueprint $table) {
            // Drop redundant columns from header table
            if (Schema::hasColumn('task_adjustments', 'service_id')) {
                // Check if index exists before dropping
                $table->dropColumn('service_id');
            }
            if (Schema::hasColumn('task_adjustments', 'raw_material_id')) {
                $table->dropForeign(['raw_material_id']);
                $table->dropColumn('raw_material_id');
            }
            if (Schema::hasColumn('task_adjustments', 'adjustment_type')) {
                $table->dropColumn('adjustment_type');
            }
            if (Schema::hasColumn('task_adjustments', 'qty')) {
                $table->dropColumn('qty');
            }
            if (Schema::hasColumn('task_adjustments', 'reason')) {
                $table->dropColumn('reason');
            }
            if (Schema::hasColumn('task_adjustments', 'previous_stock')) {
                $table->dropColumn('previous_stock');
            }
            if (Schema::hasColumn('task_adjustments', 'new_stock')) {
                $table->dropColumn('new_stock');
            }
        });

        Schema::table('task_adjustment_items', function (Blueprint $table) {
            // Drop unused columns from details table
            if (Schema::hasColumn('task_adjustment_items', 'material_category')) {
                $table->dropColumn('material_category');
            }
            if (Schema::hasColumn('task_adjustment_items', 'uom_id')) {
                $table->dropForeign(['uom_id']);
                $table->dropColumn('uom_id');
            }
            if (Schema::hasColumn('task_adjustment_items', 'store_id')) {
                $table->dropColumn('store_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_adjustments', function (Blueprint $table) {
            $table->unsignedBigInteger('service_id')->nullable()->after('task_id');
            $table->string('adjustment_type')->nullable()->after('service_id');
            $table->decimal('qty', 15, 2)->nullable()->after('adjustment_type');
            $table->text('reason')->nullable()->after('qty');
            $table->decimal('previous_stock', 15, 2)->nullable()->after('qty');
            $table->decimal('new_stock', 15, 2)->nullable()->after('previous_stock');
            $table->foreign('service_id')->references('id')->on('production_services');
        });

        Schema::table('task_adjustment_items', function (Blueprint $table) {
            $table->string('material_category')->nullable()->after('raw_material_id');
            $table->unsignedBigInteger('uom_id')->nullable()->after('qty');
            $table->unsignedBigInteger('store_id')->nullable()->after('uom_id');
            $table->foreign('uom_id')->references('id')->on('uoms');
        });
    }
};
