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
        Schema::table('barcode_masters', function (Blueprint $table) {
            $table->unsignedBigInteger('job_card_entry_id')->nullable()->after('id');
            $table->foreign('job_card_entry_id')->references('id')->on('job_card_entries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barcode_masters', function (Blueprint $table) {
            $table->dropForeign(['job_card_entry_id']);
            $table->dropColumn('job_card_entry_id');
        });
    }
};
