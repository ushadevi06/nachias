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
        Schema::create('tickets', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->string('ticket_no')->unique();
            $table->string('subject');
            $table->text('description')->nullable();
            $table->foreignId('ticket_cat_id')->constrained('ticket_categories');
            $table->string('priority');
            $table->foreignId('requester_id')->constrained('users');
            $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('operation_stage_id')->nullable()->constrained('operation_stages');
            $table->foreignId('assigned_to_id')->nullable()->constrained('users');
            $table->date('due_date')->nullable();
            $table->string('status')->default('Active');
            $table->string('attachment')->nullable();
            $table->text('remarks')->nullable();
            $table->text('resolution_details')->nullable();
            $table->date('resolved_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
