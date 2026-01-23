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
        Schema::create('task_receives', function (Blueprint $table) {
            $table->id();
            $table->string('task_receive_no')->unique();
            $table->unsignedBigInteger('task_id');
            $table->date('received_date')->nullable();
            $table->unsignedBigInteger('received_from')->nullable(); 
            $table->string('received_store')->nullable();
            $table->decimal('good_qty', 10, 2)->default(0);
            $table->decimal('rework_qty', 10, 2)->default(0);
            $table->decimal('wastage_qty', 10, 2)->default(0);
            $table->string('qc_status')->default('Pending');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_receives');
    }
};
