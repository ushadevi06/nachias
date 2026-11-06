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
        if (Schema::hasTable('leaves') && !Schema::hasColumn('leaves', 'attachment')) {
            Schema::table('leaves', function (Blueprint $table) {
                $table->string('attachment', 255)->nullable()->after('reason');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('leaves') && Schema::hasColumn('leaves', 'attachment')) {
            Schema::table('leaves', function (Blueprint $table) {
                $table->dropColumn('attachment');
            });
        }
    }
};
