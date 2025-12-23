<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->string('code')->unique();
            $table->string('mobile_no')->nullable();
            $table->string('email')->nullable();
            $table->string('website_url')->nullable();

            $table->string('transport_name')->nullable();
            $table->string('booking_area')->nullable();
            $table->string('stores')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');

            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('place_id')->nullable();

            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('address_line_3')->nullable();
            $table->string('zip_code')->nullable();

            $table->string('contact_person_name')->nullable();
            $table->string('designation')->nullable();
            $table->string('contact_mobile_no')->nullable();
            $table->string('contact_email')->nullable();

            $table->unsignedBigInteger('purchase_commission_agent_id')->nullable();
            $table->decimal('commission_percentage', 8, 2)->nullable();

            $table->string('gst_no')->nullable();
            $table->unsignedBigInteger('tax_type_id')->nullable();
            $table->string('pan_no')->nullable();
            $table->string('ecc_no')->nullable();

            $table->decimal('credit_limit', 10, 2)->nullable();
            $table->string('payment_terms')->nullable();

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
}
