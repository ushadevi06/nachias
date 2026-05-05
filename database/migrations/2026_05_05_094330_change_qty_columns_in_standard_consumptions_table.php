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
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE standard_consumptions MODIFY fs_qty DOUBLE NOT NULL DEFAULT 0');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE standard_consumptions MODIFY hs_qty DOUBLE NOT NULL DEFAULT 0');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE standard_consumptions MODIFY fs_qty DECIMAL(10,4) NOT NULL DEFAULT 0');
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE standard_consumptions MODIFY hs_qty DECIMAL(10,4) NOT NULL DEFAULT 0');
    }
};
