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
        Schema::create('payments', function (Blueprint $schema) {
            $schema->id();
            $schema->string('payment_no', 50)->unique();
            $schema->string('payment_type', 50); 
            $schema->string('reference_type', 50)->nullable(); 
            $schema->bigInteger('reference_id')->unsigned()->nullable();
            $schema->string('reference_no', 100)->nullable(); 
            $schema->string('payment_mode', 50);
            $schema->decimal('amount', 15, 2);
            $schema->date('payment_date');
            $schema->string('transaction_no', 100)->nullable();
            $schema->string('bank_name', 100)->nullable();
            $schema->string('cheque_no', 100)->nullable();
            $schema->date('cheque_date')->nullable();
            $schema->string('attachment')->nullable();
            $schema->text('remarks')->nullable();
            $schema->bigInteger('created_by')->unsigned()->nullable();
            $schema->bigInteger('updated_by')->unsigned()->nullable();
            $schema->timestamps();
            $schema->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};