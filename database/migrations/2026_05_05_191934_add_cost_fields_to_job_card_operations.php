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
            $table->decimal('rate', 15, 2)->default(0)->after('remarks');
            $table->decimal('total_cost', 15, 2)->default(0)->after('rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_card_operations', function (Blueprint $table) {
            $table->dropColumn(['rate', 'total_cost']);
        });
    }
};
