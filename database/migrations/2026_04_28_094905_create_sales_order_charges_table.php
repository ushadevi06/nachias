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
        Schema::create('sales_order_charges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained('sales_orders')->onDelete('cascade');
            $table->unsignedBigInteger('charge_id')->nullable();
            $table->string('charge_name')->nullable();
            $table->decimal('charge_amount', 15, 2)->default(0);
            $table->enum('tax_type', ['Pre-GST', 'Post-GST'])->default('Post-GST');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_order_charges');
    }
};
