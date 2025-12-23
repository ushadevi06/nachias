<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            // Employee Basic
            $table->string('emp_id')->unique();
            $table->foreignId('department_id');
            $table->foreignId('role_id');
            $table->foreignId('blood_group_id')->nullable();

            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->date('date_of_joining')->nullable();

            $table->string('father_name')->nullable();
            $table->string('father_phone')->nullable();

            $table->string('profile_image')->nullable();
            $table->string('password');

            $table->enum('status', ['Active', 'Inactive'])->default('Active');

            // Address
            $table->string('country')->nullable();
            $table->foreignId('state_id')->nullable();
            $table->foreignId('city_id')->nullable();
            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->string('zipcode')->nullable();

            // Emergency Contact
            $table->string('contact_person_name')->nullable();
            $table->string('contact_person_phone')->nullable();

            // Salary
            $table->decimal('basic_salary', 10, 2)->nullable();
            $table->decimal('hra', 10, 2)->nullable();
            $table->decimal('allowances', 10, 2)->nullable();
            $table->decimal('deductions', 10, 2)->nullable();
            $table->decimal('gross_salary', 10, 2)->nullable();
            $table->decimal('net_salary', 10, 2)->nullable();

            // Documents
            $table->string('esi_document')->nullable();
            $table->string('pf_document')->nullable();
            $table->string('aadhaar_document')->nullable();
            $table->string('pan_document')->nullable();

            // Bank Details
            $table->string('account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('ifsc_code')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
