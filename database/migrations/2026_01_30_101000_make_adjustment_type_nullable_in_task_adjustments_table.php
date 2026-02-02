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
            $table->string('adjustment_type')->nullable()->change();
            $table->decimal('qty', 15, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_adjustments', function (Blueprint $table) {
            $table->string('adjustment_type')->nullable(false)->change();
            $table->decimal('qty', 15, 2)->nullable(false)->change();
        });
    }
};
