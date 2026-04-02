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
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->integer('purchase_commission_agent_id')->nullable()->after('purchase_order_id');
            $table->decimal('commission', 5, 2)->default(0)->after('purchase_commission_agent_id');
            $table->decimal('commission_amount', 15, 2)->default(0)->after('commission');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->dropColumn(['purchase_commission_agent_id', 'commission', 'commission_amount']);
        });
    }
};
