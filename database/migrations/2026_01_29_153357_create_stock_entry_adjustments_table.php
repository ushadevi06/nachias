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
        Schema::create('stock_entry_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('adjustment_no')->unique();
            $table->foreignId('stock_entry_item_id')->constrained('stock_entry_items');
            $table->foreignId('raw_material_id')->constrained('raw_materials');
            $table->decimal('qty', 15, 2);
            $table->decimal('previous_stock', 15, 2);
            $table->decimal('new_stock', 15, 2);
            $table->string('approved_by');
            $table->text('reason');
            $table->string('status')->default('Posted');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_entry_adjustments');
    }
};
