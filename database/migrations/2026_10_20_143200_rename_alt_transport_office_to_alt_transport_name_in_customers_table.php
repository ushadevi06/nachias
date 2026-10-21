<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('customers', 'alt_transport_office')) {
            DB::statement('ALTER TABLE `customers` CHANGE `alt_transport_office` `alt_transport_name` VARCHAR(100) NULL');
        } elseif (!Schema::hasColumn('customers', 'alt_transport_name')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->string('alt_transport_name', 100)->nullable()->after('transport_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('customers', 'alt_transport_name')) {
            DB::statement('ALTER TABLE `customers` CHANGE `alt_transport_name` `alt_transport_office` VARCHAR(100) NULL');
        }
    }
};
