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
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->string('billing_address',255)->nullable()->after('store_id');
            $table->string('shipping_address',255)->nullable()->after('billing_address');
            $table->string('payment_terms',255)->nullable()->after('shipping_address');
            
            $table->string('transporter_name',255)->nullable()->after('dispatch_from');
            $table->enum('freight_type', ['Paid', 'To Pay'])->nullable()->after('transporter_name');
            $table->decimal('freight_amount', 10, 2)->nullable()->default(0)->after('freight_type');
            $table->string('eway_bill_no', 50)->nullable()->after('freight_amount');
            $table->string('lr_no', 50)->nullable()->after('eway_bill_no');
            $table->string('dispatch_through',255)->nullable()->after('lr_no');
            
            $table->unsignedBigInteger('approved_by')->nullable()->after('status');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->datetime('approved_date')->nullable()->after('approved_by');
            
            $table->string('terms_conditions',255)->nullable()->after('internal_remarks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_orders', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'billing_address', 'shipping_address', 'payment_terms',
                'transporter_name', 'freight_type', 'freight_amount',
                'eway_bill_no', 'lr_no', 'dispatch_through',
                'approved_by', 'approved_date', 'terms_conditions'
            ]);
        });
    }
};
