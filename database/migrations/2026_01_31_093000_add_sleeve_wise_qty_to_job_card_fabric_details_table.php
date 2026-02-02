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
            $table->decimal('fs_qty', 10, 2)->nullable()->after('n_patti');
            $table->decimal('hs_qty', 10, 2)->nullable()->after('fs_qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_card_fabric_details', function (Blueprint $table) {
            $table->dropColumn(['fs_qty', 'hs_qty']);
        });
    }
};
