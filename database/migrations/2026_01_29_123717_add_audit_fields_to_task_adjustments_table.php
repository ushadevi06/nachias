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
        Schema::table('task_adjustments', function (Blueprint $table) {
            $table->string('status')->default('Draft')->after('reason'); // Draft, Posted
            $table->decimal('previous_stock', 15, 2)->nullable()->after('qty');
            $table->decimal('new_stock', 15, 2)->nullable()->after('previous_stock');
            $table->string('attachment')->nullable()->after('reason');
            $table->string('reference_no')->nullable()->after('adjustment_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_adjustments', function (Blueprint $table) {
            //
        });
    }
};
