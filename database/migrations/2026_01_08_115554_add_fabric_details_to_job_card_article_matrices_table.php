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
        Schema::table('job_card_article_matrices', function (Blueprint $table) {
            $table->string('width')->nullable()->after('art_no');
            $table->string('mtr')->nullable()->after('width');
            $table->string('in_out')->nullable()->after('mtr');
            $table->string('n_patti')->nullable()->after('in_out');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_card_article_matrices', function (Blueprint $table) {
            $table->dropColumn(['width', 'mtr', 'in_out', 'n_patti']);
        });
    }
};
