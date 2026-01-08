<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_card_entries', function (Blueprint $table) {
            $table->id();
            $table->string('job_card_number')->unique();
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->unsignedBigInteger('season_id')->nullable();
            $table->unsignedBigInteger('process_group_id')->nullable();
            $table->date('job_card_date');
            $table->decimal('total_qty_fs', 15, 2)->default(0);
            $table->decimal('total_qty_hs', 15, 2)->default(0);
            $table->decimal('grand_total_qty', 15, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->enum('status', ['Draft', 'Pending', 'In Progress', 'Completed'])->default('Draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('set null');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('set null');
            $table->foreign('season_id')->references('id')->on('seasons')->onDelete('set null');
            $table->foreign('process_group_id')->references('id')->on('process_groups')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_card_entries');
    }
};
