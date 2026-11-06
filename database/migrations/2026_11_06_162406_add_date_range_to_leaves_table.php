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
        if (Schema::hasTable('leaves')) {
            Schema::table('leaves', function (Blueprint $table) {
                if (!Schema::hasColumn('leaves', 'start_date')) {
                    $table->date('start_date')->nullable()->after('leave_type');
                }
                if (!Schema::hasColumn('leaves', 'end_date')) {
                    $table->date('end_date')->nullable()->after('start_date');
                }
                if (!Schema::hasColumn('leaves', 'no_of_days')) {
                    $table->decimal('no_of_days', 5, 1)->default(1.0)->after('end_date');
                }
            });

            // Backfill existing records
            \Illuminate\Support\Facades\DB::statement("
                UPDATE `leaves` 
                SET `start_date` = COALESCE(`start_date`, `leave_date`),
                    `end_date` = COALESCE(`end_date`, `leave_date`),
                    `no_of_days` = COALESCE(`no_of_days`, 1.0)
                WHERE `start_date` IS NULL
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('leaves')) {
            Schema::table('leaves', function (Blueprint $table) {
                if (Schema::hasColumn('leaves', 'start_date')) {
                    $table->dropColumn('start_date');
                }
                if (Schema::hasColumn('leaves', 'end_date')) {
                    $table->dropColumn('end_date');
                }
                if (Schema::hasColumn('leaves', 'no_of_days')) {
                    $table->dropColumn('no_of_days');
                }
            });
        }
    }
};
