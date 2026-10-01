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
            if (!Schema::hasColumn('debit_notes', 'reference_no')) {
                $table->string('reference_no', 100)->nullable()->after('debit_note_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('debit_notes', function (Blueprint $table) {
            if (Schema::hasColumn('debit_notes', 'reference_no')) {
                $table->dropColumn('reference_no');
            }
        });
    }
};
