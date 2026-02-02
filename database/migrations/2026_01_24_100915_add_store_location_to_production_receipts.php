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
        Schema::table('production_receipts', function (Blueprint $table) {
            $table->unsignedBigInteger('store_location_id')->nullable()->after('store_type_id');
            $table->foreign('store_location_id')->references('id')->on('store_locations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_receipts', function (Blueprint $table) {
            $table->dropForeign(['store_location_id']);
            $table->dropColumn('store_location_id');
        });
    }
};
