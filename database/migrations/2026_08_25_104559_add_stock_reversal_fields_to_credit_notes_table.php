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
        Schema::table('credit_notes', function (Blueprint $table) {
            $table->boolean('is_stock_updated')->default(0)->after('status');
        });

        Schema::table('credit_note_items', function (Blueprint $table) {
            $table->boolean('add_to_inventory')->default(1)->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_notes', function (Blueprint $table) {
            $table->dropColumn('is_stock_updated');
        });

        Schema::table('credit_note_items', function (Blueprint $table) {
            $table->dropColumn('add_to_inventory');
        });
    }
};
