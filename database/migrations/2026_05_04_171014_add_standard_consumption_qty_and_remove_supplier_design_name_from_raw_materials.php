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
        Schema::table('raw_materials', function (Blueprint $table) {
            $table->decimal('standard_consumption_qty', 10, 4)->nullable()->default(0)->after('min_stock');
            $table->dropColumn('supplier_design_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raw_materials', function (Blueprint $table) {
            $table->dropColumn('standard_consumption_qty');
            $table->string('supplier_design_name', 150)->nullable();
        });
    }
};
