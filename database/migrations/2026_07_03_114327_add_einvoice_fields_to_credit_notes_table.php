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
        Schema::table('credit_notes', function (Blueprint $table) {
            $table->string('irn')->nullable()->after('grand_total');
            $table->string('ack_no')->nullable()->after('irn');
            $table->dateTime('ack_date')->nullable()->after('ack_no');
            $table->text('signed_qr_code')->nullable()->after('ack_date');
            $table->string('einvoice_status')->nullable()->after('signed_qr_code');
            $table->string('eway_bill_no')->nullable()->after('einvoice_status');
            $table->dateTime('eway_bill_date')->nullable()->after('eway_bill_no');
            $table->dateTime('eway_bill_valid_till')->nullable()->after('eway_bill_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_notes', function (Blueprint $table) {
            $table->dropColumn([
                'irn',
                'ack_no',
                'ack_date',
                'signed_qr_code',
                'einvoice_status',
                'eway_bill_no',
                'eway_bill_date',
                'eway_bill_valid_till'
            ]);
        });
    }
};
