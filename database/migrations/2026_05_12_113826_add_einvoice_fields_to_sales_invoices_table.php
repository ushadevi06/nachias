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
            $table->string('irn', 64)->nullable()->after('invoice_status');
            $table->string('ack_no')->nullable()->after('irn');
            $table->dateTime('ack_date')->nullable()->after('ack_no');
            $table->text('signed_qr_code')->nullable()->after('ack_date');
            
            $table->string('eway_bill_no')->nullable()->after('signed_qr_code');
            $table->dateTime('eway_bill_date')->nullable()->after('eway_bill_no');
            $table->dateTime('eway_bill_valid_till')->nullable()->after('eway_bill_date');
            
            $table->string('vehicle_no')->nullable()->after('eway_bill_valid_till');
            $table->string('transporter_id')->nullable()->after('vehicle_no');
            $table->string('transport_mode')->nullable()->after('transporter_id');
            $table->integer('transport_distance')->nullable()->after('transport_mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->dropColumn([
                'irn', 'ack_no', 'ack_date', 'signed_qr_code', 
                'eway_bill_no', 'eway_bill_date', 'eway_bill_valid_till',
                'vehicle_no', 'transporter_id', 'transport_mode', 'transport_distance'
            ]);
        });
    }
};
