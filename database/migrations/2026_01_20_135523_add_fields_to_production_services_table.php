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
        Schema::table('production_services', function (Blueprint $table) {
            $table->enum('applies_to', ['ALL', 'Full Sleeve', 'Half Sleeve', 'Both'])->default('ALL')->after('status');
            $table->enum('base_quantity_source', ['Total Qty', 'FS Qty', 'HS Qty'])->default('Total Qty')->after('applies_to');
            $table->decimal('multiplier', 10, 2)->default(1.00)->after('base_quantity_source');
            $table->string('uom', 20)->default('PCS')->after('multiplier');
            $table->enum('is_mandatory', ['Yes', 'No'])->default('Yes')->after('uom');
            $table->integer('sequence_no')->default(0)->after('is_mandatory');
            $table->enum('allow_partial_receipt', ['Yes', 'No'])->default('No')->after('sequence_no');
            $table->decimal('standard_rate', 10, 2)->nullable()->after('allow_partial_receipt');
            $table->string('rate_uom', 20)->nullable()->after('standard_rate');
            $table->enum('cost_category', ['Labor', 'Machine', 'Overhead'])->nullable()->after('rate_uom');
            $table->enum('auto_generate', ['Yes', 'No'])->default('Yes')->after('cost_category');
            $table->enum('allow_qty_override', ['Yes', 'No'])->default('No')->after('auto_generate');
            $table->text('description')->nullable()->after('allow_qty_override');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_services', function (Blueprint $table) {
            $table->dropColumn([
                'applies_to',
                'base_quantity_source',
                'multiplier',
                'uom',
                'is_mandatory',
                'sequence_no',
                'allow_partial_receipt',
                'standard_rate',
                'rate_uom',
                'cost_category',
                'auto_generate',
                'allow_qty_override',
                'description'
            ]);
        });
    }
};
