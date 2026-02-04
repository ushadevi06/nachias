<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_consumable_stock_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_consumable_issue_item_id');
            $table->unsignedBigInteger('stock_entry_item_id');
            $table->decimal('qty', 15, 2);
            $table->timestamps();

            $table->foreign('stock_consumable_issue_item_id', 'sc_stock_detail_issue_item_fk')
                ->references('id')->on('stock_consumable_issue_items')->onDelete('cascade');
            $table->foreign('stock_entry_item_id')->references('id')->on('stock_entry_items');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_consumable_stock_details');
    }
};
