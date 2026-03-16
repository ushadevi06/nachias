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
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn('dispatch_from');
            $table->unsignedBigInteger('dispatch_from_id')->nullable()->after('transport_mode_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn('dispatch_from_id');
            $table->string('dispatch_from', 255)->nullable()->after('transport_mode_id');
        });
    }
};
