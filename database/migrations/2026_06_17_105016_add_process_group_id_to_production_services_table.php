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
            $table->unsignedBigInteger('process_group_id')->nullable()->after('operation_stage_id');
            $table->foreign('process_group_id')->references('id')->on('process_groups')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_services', function (Blueprint $table) {
            $table->dropForeign(['process_group_id']);
            $table->dropColumn('process_group_id');
        });
    }
};
