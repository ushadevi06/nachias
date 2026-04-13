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
        Schema::table('job_card_lay_marks', function (Blueprint $table) {
            $table->decimal('no_of_lay', 10, 2)->nullable()->after('lay_mark_meter');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_card_lay_marks', function (Blueprint $table) {
            $table->dropColumn('no_of_lay');
        });
    }
};
