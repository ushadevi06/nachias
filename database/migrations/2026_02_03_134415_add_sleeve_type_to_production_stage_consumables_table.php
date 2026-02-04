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
            $table->enum('sleeve_type', ['All', 'F/S', 'H/S'])->default('All')->after('uom_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_stage_consumables', function (Blueprint $table) {
            //
        });
    }
};
