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
            if (!Schema::hasColumn('debit_notes', 'other_charges')) {
                $table->decimal('other_charges', 15, 2)->nullable()->after('sub_total');
            } else {
                $table->decimal('other_charges', 15, 2)->nullable()->change();
            }
            $table->decimal('tax_amount', 15, 2)->nullable()->change();
            $table->decimal('igst_percent', 5, 2)->nullable()->change();
            $table->decimal('cgst_percent', 5, 2)->nullable()->change();
            $table->decimal('sgst_percent', 5, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('debit_notes', function (Blueprint $table) {
            //
        });
    }
};
