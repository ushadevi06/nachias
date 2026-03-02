<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_orders', function (Blueprint $table) {
            $table->id();
            $table->string('so_no',50)->unique();
            $table->date('so_date');
            $table->date('request_date')->nullable();
            $table->string('order_type',50)->default('Regular');
            $table->unsignedBigInteger('season_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_po_ref',50)->nullable();
            $table->unsignedBigInteger('store_id')->nullable();
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->date('delivery_date')->nullable();
            $table->string('shipping_method',50)->nullable();
            $table->string('transport_mode',50)->nullable();
            $table->string('dispatch_from', 255)->nullable();
            $table->enum('status', ['Draft', 'Approved', 'Pending', 'In Production', 'Dispatched', 'Cancelled'])->default('Draft');
            $table->decimal('total_qty', 15, 2)->default(0);
            $table->decimal('sub_total_qty', 15, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('taxable_amount', 15, 2)->default(0);
            $table->boolean('other_state')->default(false);
            $table->decimal('igst_percent', 5, 2)->default(0);
            $table->decimal('cgst_percent', 5, 2)->default(18);
            $table->decimal('sgst_percent', 5, 2)->default(9);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->string('round_off_type',50)->default('Add');
            $table->decimal('round_off', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('internal_remarks',255)->nullable();
            $table->string('attachment',255)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_orders');
    }
};
