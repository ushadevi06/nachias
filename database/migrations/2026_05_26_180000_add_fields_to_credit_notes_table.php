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
        Schema::table('credit_notes', function (Blueprint $table) {
            $table->text('reason_detail')->nullable()->after('reason');
            $table->unsignedBigInteger('zone_id')->nullable()->after('customer_id');
            $table->unsignedBigInteger('agent_id')->nullable()->after('zone_id');
            $table->text('show_fields')->nullable()->after('status');
            $table->decimal('discount_percent', 5, 2)->default(0.00)->after('sub_total');
            $table->decimal('discount', 15, 2)->default(0.00)->after('discount_percent');
            $table->decimal('other_charges', 15, 2)->default(0.00)->after('tax_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_notes', function (Blueprint $table) {
            $table->dropColumn([
                'reason_detail',
                'zone_id',
                'agent_id',
                'show_fields',
                'discount_percent',
                'discount',
                'other_charges',
            ]);
        });
    }
};
