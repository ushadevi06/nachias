<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('grn_entry_items', 'color_id')) {
            Schema::table('grn_entry_items', function (Blueprint $table) {
                $table->unsignedBigInteger('color_id')->nullable()->after('fabric_type_id');
                $table->foreign('color_id')->references('id')->on('colors')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('grn_entry_items', 'color_id')) {
            Schema::table('grn_entry_items', function (Blueprint $table) {
                $table->dropForeign(['color_id']);
                $table->dropColumn('color_id');
            });
        }
    }
};
