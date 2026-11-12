<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('stock_taking_sessions') && !Schema::hasColumn('stock_taking_sessions', 'store_type_id')) {
            Schema::table('stock_taking_sessions', function (Blueprint $table) {
                $table->unsignedBigInteger('store_type_id')->nullable()->after('warehouse_id')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('stock_taking_sessions') && Schema::hasColumn('stock_taking_sessions', 'store_type_id')) {
            Schema::table('stock_taking_sessions', function (Blueprint $table) {
                $table->dropColumn('store_type_id');
            });
        }
    }
};

