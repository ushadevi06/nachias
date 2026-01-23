<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_receipts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_id')->nullable();
            $table->unsignedBigInteger('job_card_id');
            $table->string('receipt_no')->nullable();
            $table->date('receipt_date');
            $table->string('doc_no')->nullable();
            $table->date('doc_date')->nullable();
            $table->unsignedBigInteger('store_type_id');
            $table->enum('status', ['Draft', 'Posted'])->default('Draft');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index('production_id');
            $table->index('job_card_id');
            $table->index('store_type_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_receipts');
    }
};
