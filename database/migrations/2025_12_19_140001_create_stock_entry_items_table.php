<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * STOCK ENTRY ITEMS DESIGN RULES:
     * ===============================
     * - stock_type per item (not header)
     * - qty direction enforced by entry_type in header
     * - store_location from GRN (readonly when linked) or based on transfer direction
     */
    public function up(): void
    {
        Schema::create('stock_entry_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_entry_id')->constrained()->onDelete('cascade');
            $table->enum('stock_type', ['raw_material', 'finished_goods']);
            $table->foreignId('grn_entry_item_id')->nullable()->constrained('grn_entry_items');
            $table->foreignId('raw_material_id')->nullable()->constrained('raw_materials');
            
            /**
             * TEMPORARY: Static string for finished goods
             * TODO: Replace with FK to finished_goods table when available
             * 
             * Future migration should:
             * 1. Create finished_goods table
             * 2. Add: $table->foreignId('finished_good_id')->nullable()->constrained();
             * 3. Migrate existing finished_item_code data
             * 4. Drop finished_item_code column
             */
            $table->string('finished_item_code')->nullable()
                  ->comment('TEMPORARY: Replace with finished_good_id FK when finished_goods table exists');
            
            // Category & Location
            $table->foreignId('store_category_id')->nullable()->constrained('store_categories');
            
            /**
             * STORE LOCATION RULES:
             * - Purchase Receipt: store_location_id = GRN item's store_location (readonly)
             * - Transfer Issue: store_location_id = from_store_location_id (source)
             * - Transfer Receipt: store_location_id = to_store_location_id (destination)
             * - Production Issue: store_location_id = raw material store
             * - Production Receipt: store_location_id = finished goods store
             */
            $table->foreignId('store_location_id')->constrained('store_locations');
            $table->foreignId('uom_id')->nullable()->constrained('uoms');
            
            /**
             * QUANTITY DIRECTION RULES (enforced by UI + backend):
             * Entry Type          | qty_in | qty_out
             * -------------------|--------|--------
             * Purchase Receipt   | ✓      | ✗
             * Transfer Receipt   | ✓      | ✗
             * Transfer Issue     | ✗      | ✓
             * Production Issue   | ✗      | ✓
             * Production Receipt | ✓      | ✗
             * Stock Adjustment   | ✓ or ✓ | (one only)
             * Stock Conversion   | ✓      | ✓
             */
            $table->decimal('qty_in', 15, 2)->default(0);
            $table->decimal('qty_out', 15, 2)->default(0);
            
            // No price/amount here - valuation handled by stock ledger
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_entry_items');
    }
};
