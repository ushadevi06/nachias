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
            $table->integer('qc_checked_qty')->default(0)->after('wastage_qty');
            $table->integer('qc_passed_qty')->default(0)->after('qc_checked_qty');
            $table->integer('qc_rejected_qty')->default(0)->after('qc_passed_qty');
            $table->string('qc_status')->default('Pending')->after('qc_rejected_qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_assign_employees', function (Blueprint $table) {
            $table->dropColumn(['qc_checked_qty', 'qc_passed_qty', 'qc_rejected_qty', 'qc_status']);
        });
    }
};
