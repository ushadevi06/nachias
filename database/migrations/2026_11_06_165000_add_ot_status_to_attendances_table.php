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
        if (Schema::hasTable('attendances') && !Schema::hasColumn('attendances', 'ot_status')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->string('ot_status', 50)->default('Pending')->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('attendances') && Schema::hasColumn('attendances', 'ot_status')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropColumn('ot_status');
            });
        }
    }
};

