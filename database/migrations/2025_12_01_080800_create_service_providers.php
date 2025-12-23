<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_providers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_type_id');
            $table->string('name');
            $table->string('code');
            $table->string('email')->nullable();
            $table->string('mobile_no');
            $table->string('zip_code')->nullable();
            $table->string('website_url')->nullable();
            $table->enum('service_rate', ['Per Agent', 'Job Type']);
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('state_id');
            $table->unsignedBigInteger('city_id');
            $table->unsignedBigInteger('place_id');
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('contact_person_name')->nullable();
            $table->string('designation')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('pan_no')->nullable();
            $table->string('gst_no')->nullable();
            $table->text('remarks')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_acc_no')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->text('payment_terms')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_providers');
    }
};
