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
            $table->unsignedBigInteger('operation_stage_id')->nullable()->after('service_code');
            $table->foreign('operation_stage_id')->references('id')->on('operation_stages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_services', function (Blueprint $table) {
            $table->dropForeign(['operation_stage_id']);
            $table->dropColumn('operation_stage_id');
        });
    }
};
