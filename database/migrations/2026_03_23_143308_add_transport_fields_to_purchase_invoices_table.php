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
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->string('transport')->nullable()->after('attachments');
            $table->string('destination')->nullable()->after('transport');
            $table->string('lr_no')->nullable()->after('destination');
            $table->date('lr_date')->nullable()->after('lr_no');
            $table->string('indent_no')->nullable()->after('lr_date');
            $table->date('indent_date')->nullable()->after('indent_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_invoices', function (Blueprint $table) {
            $table->dropColumn([
                'transport',
                'destination',
                'lr_no',
                'lr_date',
                'indent_no',
                'indent_date'
            ]);
        });
    }
};
