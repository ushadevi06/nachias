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
        Schema::table('resources', function (Blueprint $table) {
            $table->dropForeign(['process_id']);
            $table->dropColumn(['process_id', 'capacity_per_day', 'capacity_per_hour']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->unsignedBigInteger('process_id')->after('service_provider_id');
            $table->decimal('capacity_per_day', 15, 2)->after('process_id');
            $table->decimal('capacity_per_hour', 15, 2)->nullable()->after('capacity_per_day');
            
            $table->foreign('process_id')->references('id')->on('production_services');
        });
    }
};
