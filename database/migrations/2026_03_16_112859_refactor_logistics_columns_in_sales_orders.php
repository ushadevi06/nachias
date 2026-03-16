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
            $table->dropColumn(['shipping_method', 'transport_mode']);
            $table->unsignedBigInteger('shipping_method_id')->nullable()->after('agent_id');
            $table->unsignedBigInteger('transport_mode_id')->nullable()->after('shipping_method_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_method_id', 'transport_mode_id']);
            $table->string('shipping_method', 50)->nullable()->after('agent_id');
            $table->string('transport_mode', 50)->nullable()->after('shipping_method');
        });
    }
};
