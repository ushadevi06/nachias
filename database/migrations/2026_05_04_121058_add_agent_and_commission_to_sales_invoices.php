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
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->nullable()->after('customer_id');
            $table->unsignedBigInteger('agent_id')->nullable()->after('store_id');
            $table->decimal('commission_percent', 10, 2)->default(0)->after('discount');
            $table->decimal('commission_amount', 15, 2)->default(0)->after('commission_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->dropColumn(['store_id', 'agent_id', 'commission_percent', 'commission_amount']);
        });
    }
};
