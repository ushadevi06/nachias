<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_consumable_issue_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_consumable_issue_id');
            $table->unsignedBigInteger('raw_material_id')->nullable();
            $table->unsignedBigInteger('stock_entry_item_id')->nullable(); // Main stock item used
            $table->decimal('qty_issued', 15, 2)->default(0);
            $table->decimal('qty_returned', 15, 2)->default(0);
            $table->decimal('net_consumption', 15, 2)->default(0); // issued - returned
            $table->string('return_reason')->nullable(); // Excess, Damage, Quality Issue
            $table->unsignedBigInteger('uom_id')->nullable();
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('total_value', 15, 2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('stock_consumable_issue_id')->references('id')->on('stock_consumable_issues')->onDelete('cascade');
            $table->foreign('raw_material_id')->references('id')->on('raw_materials');
            $table->foreign('stock_entry_item_id')->references('id')->on('stock_entry_items');
            $table->foreign('uom_id')->references('id')->on('uoms');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_consumable_issue_items');
    }
};
