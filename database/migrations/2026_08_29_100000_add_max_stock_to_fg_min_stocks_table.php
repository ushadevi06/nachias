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
        Schema::table('fg_min_stocks', function (Blueprint $table) {
            $table->decimal('max_stock', 10, 2)->default(0.00)->after('min_stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fg_min_stocks', function (Blueprint $table) {
            $table->dropColumn('max_stock');
        });
    }
};
