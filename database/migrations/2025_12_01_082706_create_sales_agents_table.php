<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_agents', function (Blueprint $table) {
            $table->id();
            $table->string('agent_type');
            $table->string('name');
            $table->string('code')->unique();
            $table->string('email');
            $table->string('mobile_no');
            $table->string('status')->default('Active');
            $table->unsignedBigInteger('state_id');
            $table->unsignedBigInteger('city_id');
            $table->unsignedBigInteger('place_id')->nullable();
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('contact_person_name')->nullable();
            $table->string('designation')->nullable();
            $table->string('contact_phone_number')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('pan_no')->nullable();
            $table->string('gst_no')->nullable();
            $table->decimal('commission_value', 10, 2)->nullable();
            $table->decimal('sales_target', 12, 2)->nullable();
            $table->timestamps();

            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->foreign('place_id')->references('id')->on('places')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_agents');
    }
};
