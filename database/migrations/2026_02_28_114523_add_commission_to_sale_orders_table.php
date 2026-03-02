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
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->decimal('commission_percent', 5, 2)->nullable()->after('sub_total_qty');
            $table->decimal('commission_amount', 15, 2)->nullable()->after('commission_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->dropColumn(['commission_percent', 'commission_amount']);
        });
    }
};
