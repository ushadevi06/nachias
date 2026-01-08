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
            $table->integer('hs_46')->default(0)->after('hs_44');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_card_article_matrices', function (Blueprint $table) {
            $table->dropColumn('hs_46');
        });
    }
};
