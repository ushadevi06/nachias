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
            $table->decimal('price_fs', 10, 2)->nullable()->after('cutting_issue_unit');
            $table->decimal('price_hs', 10, 2)->nullable()->after('price_fs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_card_entries', function (Blueprint $table) {
            //
        });
    }
};
