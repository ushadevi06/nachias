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
        Schema::table('production_stage_consumables', function (Blueprint $table) {
            $table->string('art_no')->nullable()->after('stage');
            $table->string('item_type')->nullable()->after('art_no');
            $table->decimal('fs_qty', 15, 3)->default(0)->after('item_type');
            $table->decimal('hs_qty', 15, 3)->default(0)->after('fs_qty');
            $table->decimal('total_qty', 15, 3)->default(0)->after('hs_qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_stage_consumables', function (Blueprint $table) {
            $table->dropColumn(['art_no', 'item_type', 'fs_qty', 'hs_qty', 'total_qty']);
        });
    }
};
