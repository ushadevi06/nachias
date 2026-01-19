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
            $table->string('ex_1_label')->nullable()->after('hs_qty');
            $table->string('ex_2_label')->nullable()->after('ex_1_label');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_card_entries', function (Blueprint $table) {
            $table->dropColumn(['ex_1_label', 'ex_2_label']);
        });
    }
};
