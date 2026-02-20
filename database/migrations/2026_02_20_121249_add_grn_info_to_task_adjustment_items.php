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
        Schema::table('task_adjustment_items', function (Blueprint $table) {
            $table->string('grn_no')->nullable()->after('raw_material_id');
            $table->string('art_no')->nullable()->after('grn_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_adjustment_items', function (Blueprint $table) {
            $table->dropColumn(['grn_no', 'art_no']);
        });
    }
};
