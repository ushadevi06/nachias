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
        Schema::table('item_prices', function (Blueprint $table) {
            $table->string('size', 10)->nullable()->after('art_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_prices', function (Blueprint $table) {
            $table->dropColumn('size');
        });
    }
};
