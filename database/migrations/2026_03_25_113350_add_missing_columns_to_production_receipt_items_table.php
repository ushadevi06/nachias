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
        Schema::table('production_receipt_items', function (Blueprint $table) {
            if (!Schema::hasColumn('production_receipt_items', 'color')) {
                $table->string('color')->nullable()->after('size');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_receipt_items', function (Blueprint $table) {
            $table->dropColumn(['color']);
        });
    }
};
