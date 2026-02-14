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
        Schema::table('process_schedules', function (Blueprint $table) {
            $table->string('stage')->change();
            $table->foreignId('operation_stage_id')->nullable()->after('stage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('process_schedules', function (Blueprint $table) {
            $table->dropForeign(['operation_stage_id']);
            $table->dropColumn('operation_stage_id');
            $table->enum('stage', ['Stitching', 'Finishing'])->change();
        });
    }
};
