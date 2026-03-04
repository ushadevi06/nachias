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
            if (Schema::hasColumn('raw_materials', 'fabric_type_id')) {
                $table->dropColumn('fabric_type_id');
            }
            $table->string('material_type', 100)->nullable()->after('uom_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('raw_materials', function (Blueprint $table) {
            $table->dropColumn('material_type');
            $table->unsignedBigInteger('fabric_type_id')->nullable()->after('uom_id');
        });
    }
};
