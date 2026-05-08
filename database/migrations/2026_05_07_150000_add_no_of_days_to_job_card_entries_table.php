<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_card_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('job_card_entries', 'no_of_days')) {
                $table->unsignedInteger('no_of_days')->nullable()->after('delivery_date');
            }
        });

        // DB::table('job_card_entries')
        //     ->whereNotNull('job_card_date')
        //     ->whereNotNull('delivery_date')
        //     ->update([
        //         'no_of_days' => DB::raw('GREATEST(DATEDIFF(delivery_date, job_card_date), 0)')
        //     ]);
    }

    public function down(): void
    {
        Schema::table('job_card_entries', function (Blueprint $table) {
            if (Schema::hasColumn('job_card_entries', 'no_of_days')) {
                $table->dropColumn('no_of_days');
            }
        });
    }
};
