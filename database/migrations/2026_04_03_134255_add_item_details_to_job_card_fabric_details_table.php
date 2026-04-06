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
        Schema::table('job_card_fabric_details', function (Blueprint $table) {
            $table->decimal('total_qty', 15, 3)->nullable()->after('hs_qty');
            $table->decimal('used_qty', 15, 3)->nullable()->after('total_qty');
            $table->decimal('remaining_qty', 15, 3)->nullable()->after('used_qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_card_fabric_details', function (Blueprint $table) {
            $table->dropColumn(['total_qty', 'used_qty', 'remaining_qty']);
        });
    }
};
