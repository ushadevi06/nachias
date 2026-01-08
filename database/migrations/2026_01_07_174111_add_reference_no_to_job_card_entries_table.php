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
            if (!Schema::hasColumn('job_card_entries', 'reference_no')) {
                $table->string('reference_no')->nullable()->after('job_card_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_card_entries', function (Blueprint $table) {
            $table->dropColumn('reference_no');
        });
    }
};
