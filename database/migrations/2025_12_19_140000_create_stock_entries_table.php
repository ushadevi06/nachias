<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * STOCK ENTRY DESIGN RULES:
     * ========================
     * Header = why & when (document info only)
     * Items = what & how much
     * Direction = system-controlled by entry_type
     * Value = stock ledger only (no price here)
     */
    public function up(): void
    {
        Schema::create('stock_entries', function (Blueprint $table) {
            $table->id();
            $table->string('stock_entry_no')->unique();
            $table->date('stock_date');
            
            /**
             * ENTRY TYPE DIRECTION RULES:
             * - Purchase Receipt  → qty_in only
             * - Transfer Receipt  → qty_in only
             * - Transfer Issue    → qty_out only
             * - Production Issue  → qty_out only
             * - Production Receipt → qty_in only
             * - Stock Adjustment  → either (+ / −)
             * - Stock Conversion  → both (in/out for conversion)
             */
            $table->enum('entry_type', [
                'Purchase Receipt',
                'Transfer Receipt',
                'Transfer Issue',
                'Production Issue',
                'Production Receipt',
                'Stock Adjustment',
                'Stock Conversion'
            ]);
            
            // Link to GRN (optional, for Purchase Receipt)
            $table->foreignId('grn_entry_id')->nullable()->constrained('grn_entries');
            
            /**
             * STORE LOCATION RULES FOR TRANSFERS:
             * - Transfer Issue: from_store = source, to_store = destination
             * - Transfer Receipt: from_store = source, to_store = destination
             * - For items: store_location_id = from_store (issue) or to_store (receipt)
             */
            $table->foreignId('from_store_location_id')->nullable()->constrained('store_locations');
            $table->foreignId('to_store_location_id')->nullable()->constrained('store_locations');
            
            $table->text('remarks')->nullable();
            $table->string('reference_document')->nullable();
            $table->enum('status', ['Draft', 'Posted', 'Cancelled'])->default('Draft');
            
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            // CRITICAL: Prevent duplicate stock entries for same GRN
            $table->unique(['grn_entry_id', 'entry_type'], 'unique_grn_stock_entry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_entries');
    }
};
