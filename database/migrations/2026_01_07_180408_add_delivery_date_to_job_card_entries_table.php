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
            if (!Schema::hasColumn('job_card_entries', 'delivery_date')) {
                $table->date('delivery_date')->nullable()->after('job_card_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_card_entries', function (Blueprint $table) {
            if (Schema::hasColumn('job_card_entries', 'delivery_date')) {
                $table->dropColumn('delivery_date');
            }
        });
    }
};
