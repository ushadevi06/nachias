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
        Schema::table('suppliers', function (Blueprint $table) {
            $table->decimal('igst_percent', 8, 2)->nullable()->after('tax_id');
            $table->decimal('cgst_percent', 8, 2)->nullable()->after('igst_percent');
            $table->decimal('sgst_percent', 8, 2)->nullable()->after('cgst_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['igst_percent', 'cgst_percent', 'sgst_percent']);
        });
    }
};
