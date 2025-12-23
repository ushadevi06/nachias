<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('mobile_no');
            $table->string('email')->nullable();
            $table->string('website_url')->nullable();
            $table->string('transport_name')->nullable();
            $table->string('booking_area')->nullable();
            $table->string('stores')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->unsignedBigInteger('state_id');
            $table->unsignedBigInteger('city_id');
            $table->unsignedBigInteger('place_id');
            $table->text('address_line_1');
            $table->text('address_line_2')->nullable();
            $table->text('address_line_3')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('contact_person_name')->nullable();
            $table->string('designation')->nullable();
            $table->string('contact_mobile_no')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('purchase_commission_agent')->nullable();
            $table->decimal('commission', 5, 2)->default(0);
            $table->unsignedBigInteger('tax_type_id')->nullable();
            $table->string('gst_no')->nullable();
            $table->string('pan_no')->nullable();
            $table->string('ecc_no')->nullable();
            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->text('payment_terms')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('branch')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('suppliers');
    }
};
