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
        Schema::create('job_card_pending_reasons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_card_id')->index();
            $table->string('job_card_no', 100)->nullable()->index();
            $table->unsignedBigInteger('production_receipt_id')->nullable()->index();
            $table->string('reason', 100);
            $table->decimal('qty', 10, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_card_pending_reasons');
    }
};
