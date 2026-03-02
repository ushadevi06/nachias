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
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->dropColumn('round_off_amount');
            $table->decimal('round_off', 10, 2)->default(0)->after('round_off_type');
            $table->string('round_off_type', 10)->default('Add')->change(); // Add, Less
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->renameColumn('round_off', 'round_off_amount');
            $table->string('round_off_type', 10)->default('add')->change();
        });
    }
};
