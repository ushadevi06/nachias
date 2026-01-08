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
        Schema::table('job_card_operations', function (Blueprint $table) {
            if (!Schema::hasColumn('job_card_operations', 'received_by')) {
                $table->string('received_by')->nullable()->after('assigned_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_card_operations', function (Blueprint $table) {
            if (Schema::hasColumn('job_card_operations', 'received_by')) {
                $table->dropColumn('received_by');
            }
        });
    }
};
