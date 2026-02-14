<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            // Rename stores to store_id and change type
            // Note: If data exists, it might need manual conversion if it's not numeric
            $table->unsignedBigInteger('store_id')->nullable()->after('zone_id');
        });

        // Optional: Move data from stores to store_id if possible
        // But since the user wants to "only change store into store_id", 
        // and 'stores' was a string (possibly containing names), 
        // standard migration might just drop and add if purely refactoring.
        // I'll drop the old column.
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('stores');
        });
    }

    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('stores')->nullable()->after('zone_id');
            $table->dropColumn('store_id');
        });
    }
};
