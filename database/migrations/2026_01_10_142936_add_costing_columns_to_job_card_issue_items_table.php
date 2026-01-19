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
        Schema::table('job_card_issue_items', function (Blueprint $table) {
            $table->decimal('unit_price', 15, 2)->default(0)->after('produced_qty');
            $table->decimal('total_cost', 15, 2)->default(0)->after('unit_price');
            $table->decimal('cost_per_pc', 15, 2)->default(0)->after('total_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_card_issue_items', function (Blueprint $table) {
            $table->dropColumn(['unit_price', 'total_cost', 'cost_per_pc']);
        });
    }
};
