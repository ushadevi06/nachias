<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billings', function (Blueprint $table) {
            $table->id();
            $table->string('bill_no', 100);
            $table->string('billing_type')->nullable();
            $table->date('bill_date');
            $table->decimal('amount', 15, 2)->nullable();
            $table->text('reason')->nullable();
            $table->string('status')->default('Pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billings');
    }
};
