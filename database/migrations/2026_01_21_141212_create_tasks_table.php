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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_no')->unique(); 
            $table->unsignedBigInteger('production_id')->nullable();
            $table->string('production_no')->nullable();
            $table->unsignedBigInteger('job_card_entry_id')->nullable();
            $table->string('job_card_no')->nullable();
            $table->unsignedBigInteger('stage_id')->nullable(); 
            $table->unsignedBigInteger('issued_to')->nullable(); 
            $table->date('issue_date')->nullable();
            $table->decimal('issue_qty', 10, 2)->default(0);
            $table->string('issue_store')->nullable();
            $table->text('remarks')->nullable();
            $table->enum('status', ['Planned', 'In Progress', 'Completed', 'Hold'])->default('Planned');
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
        Schema::dropIfExists('tasks');
    }
};
