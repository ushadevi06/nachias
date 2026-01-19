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
            $table->integer('hs_36')->default(0)->after('fs_46');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_card_article_matrices', function (Blueprint $table) {
            $table->dropColumn('hs_36');
        });
    }
};
