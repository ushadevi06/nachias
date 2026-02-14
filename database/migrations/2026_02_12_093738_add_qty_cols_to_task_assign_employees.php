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
            $table->decimal('completed_qty', 10, 2)->default(0)->after('issue_qty');
            $table->decimal('inprogress_qty', 10, 2)->default(0)->after('completed_qty');
            $table->decimal('wastage_qty', 10, 2)->default(0)->after('inprogress_qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_assign_employees', function (Blueprint $table) {
            $table->dropColumn(['completed_qty', 'inprogress_qty', 'wastage_qty']);
        });
    }
};
