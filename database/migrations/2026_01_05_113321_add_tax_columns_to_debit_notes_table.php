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
        Schema::table('debit_notes', function (Blueprint $table) {
            $table->enum('other_state', ['Y', 'N'])->default('N')->after('supplier_id');
            $table->decimal('igst_percent', 5, 2)->default(0)->after('sub_total');
            $table->decimal('cgst_percent', 5, 2)->default(0)->after('igst_percent');
            $table->decimal('sgst_percent', 5, 2)->default(0)->after('cgst_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('debit_notes', function (Blueprint $table) {
            $table->dropColumn(['other_state', 'igst_percent', 'cgst_percent', 'sgst_percent']);
        });
    }
};
