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
        Schema::table('sale_order_items', function (Blueprint $table) {
            DB::statement('ALTER TABLE sale_order_items CHANGE size size_id BIGINT UNSIGNED NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_order_items', function (Blueprint $table) {
            DB::statement('ALTER TABLE sale_order_items CHANGE size_id size VARCHAR(255) NULL');
        });
    }
};
