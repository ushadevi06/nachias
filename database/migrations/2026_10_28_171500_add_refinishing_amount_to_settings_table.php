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
        if (Schema::hasTable('settings') && !Schema::hasColumn('settings', 'refinishing_amount')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->decimal('refinishing_amount', 12, 2)->default(0)->after('terms_and_conditions');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('settings') && Schema::hasColumn('settings', 'refinishing_amount')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('refinishing_amount');
            });
        }
    }
};

