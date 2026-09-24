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
        Schema::table('job_card_fabric_details', function (Blueprint $table) {
            if (!Schema::hasColumn('job_card_fabric_details', 'fg_art_no')) {
                $table->string('fg_art_no', 255)->nullable()->after('art_no');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_card_fabric_details', function (Blueprint $table) {
            if (Schema::hasColumn('job_card_fabric_details', 'fg_art_no')) {
                $table->dropColumn('fg_art_no');
            }
        });
    }
};
