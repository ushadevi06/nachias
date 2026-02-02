<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_consumable_issues', function (Blueprint $table) {
            $table->id();
            $table->string('issue_no')->unique();
            $table->date('issue_date');
            $table->enum('issue_type', ['Consumable Issue', 'Sales Return'])->default('Consumable Issue');
            $table->string('production_stage')->nullable(); // Cutting, Stitching, etc.
            $table->text('remarks')->nullable();
            $table->enum('status', ['Draft', 'Posted'])->default('Draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_consumable_issues');
    }
};
