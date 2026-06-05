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
        Schema::table('task_assign_employees', function (Blueprint $table) {
            $table->decimal('unit_rate', 10, 2)->nullable()->after('completed_qty');
            $table->decimal('total_cost', 15, 2)->nullable()->after('unit_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_assign_employees', function (Blueprint $table) {
            $table->dropColumn(['unit_rate', 'total_cost']);
        });
    }
};
