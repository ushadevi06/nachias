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
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_card_entry_id')->constrained('job_card_entries')->onDelete('cascade');
            $table->string('job_card_no');
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders');
            $table->string('purchase_order_no')->nullable();
            $table->foreignId('plant_id')->nullable()->constrained('service_providers');
            $table->foreignId('process_group_id')->nullable()->constrained('process_groups');
            $table->integer('full_sleeve_qty')->default(0);
            $table->integer('half_sleeve_qty')->default(0);
            $table->integer('total_planned_qty')->default(0);
            $table->date('planned_start_date')->nullable();
            $table->date('planned_end_date')->nullable();
            $table->date('expected_completion_date')->nullable();
            $table->enum('status', ['Draft', 'Confirmed', 'Closed'])->default('Draft');
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productions');
    }
};
