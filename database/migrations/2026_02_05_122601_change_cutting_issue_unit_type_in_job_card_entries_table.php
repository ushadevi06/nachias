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
            $table->unsignedBigInteger('cutting_issue_unit')->change();
        });

        Schema::table('process_schedules', function (Blueprint $table) {
            $table->unsignedBigInteger('scheduled_to')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_card_entries', function (Blueprint $table) {
            $table->string('cutting_issue_unit')->change();
        });

        Schema::table('process_schedules', function (Blueprint $table) {
            $table->string('scheduled_to')->change();
        });
    }
};
