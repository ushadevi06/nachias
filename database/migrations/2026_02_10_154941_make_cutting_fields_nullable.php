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
        Schema::table('job_card_entries', function (Blueprint $table) {
            $table->unsignedBigInteger('cutting_issue_unit')->nullable()->change();
            $table->unsignedBigInteger('cutting_master_id')->nullable()->change();
            $table->date('cutting_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_card_entries', function (Blueprint $table) {
            // Reverting to NOT NULL would require knowledge of previous state or ensuring no nulls exist.
            // For now, we allow them to remain nullable as reverting constraint is complex.
        });
    }
};
