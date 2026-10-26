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
        Schema::create('customer_opening_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable()->index();
            $table->string('customer_code', 100)->index();
            $table->string('customer_name', 255)->nullable();
            $table->decimal('open_qty', 15, 2)->default(0);
            $table->decimal('open_amount', 15, 2)->default(0);
            $table->date('as_on_date')->nullable();
            $table->string('financial_year', 50)->nullable();
            $table->string('remarks', 255)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_opening_balances');
    }
};
