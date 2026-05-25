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
            $table->decimal('mrp', 15, 2)->default(0)->after('unit_price');
        });
    }

    public function down(): void
    {
        Schema::table('production_receipt_items', function (Blueprint $table) {
            $table->dropColumn('mrp');
        });
    }
};
