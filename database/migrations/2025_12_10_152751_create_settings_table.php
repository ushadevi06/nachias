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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('email');
            $table->string('logo')->nullable();
            $table->string('phone_number', 15);
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->text('address');
            $table->integer('cgst')->default(0);
            $table->integer('sgst')->default(0);
            $table->integer('igst')->default(0);
            $table->string('pan_no', 10)->nullable();
            $table->string('gst_no', 15)->nullable();
            $table->string('cin_no', 21)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
