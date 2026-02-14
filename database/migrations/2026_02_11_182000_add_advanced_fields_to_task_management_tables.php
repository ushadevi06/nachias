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
        // User requested NOT to add standard time to production_services
        
        Schema::table('task_receives', function (Blueprint $table) {
            $table->decimal('actual_hours', 10, 2)->nullable()->after('rework_qty');
            $table->decimal('efficiency_percent', 10, 2)->nullable()->after('actual_hours');
            $table->decimal('standard_minutes', 10, 2)->nullable()->after('efficiency_percent'); // Storing it per receive instead of service table
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_task_id')->nullable()->after('task_no');
            $table->boolean('is_rework')->default(false)->after('parent_task_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_receives', function (Blueprint $table) {
            $table->dropColumn(['actual_hours', 'efficiency_percent', 'standard_minutes']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['parent_task_id', 'is_rework']);
        });
    }
};
