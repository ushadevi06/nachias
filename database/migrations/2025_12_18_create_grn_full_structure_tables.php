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
        Schema::dropIfExists('grn_entry_item_variants');
        Schema::dropIfExists('grn_entry_items');
        Schema::dropIfExists('grn_entries');

        if (!Schema::hasTable('colors')) {
            Schema::create('colors', function (Blueprint $table) {
                $table->id();
                $table->string('color_name');
                $table->enum('status', ['Active', 'Inactive'])->default('Active');
                $table->timestamps();
                $table->softDeletes();
            });
        } else {
            Schema::table('colors', function (Blueprint $table) {
                if (!Schema::hasColumn('colors', 'status')) {
                    $table->enum('status', ['Active', 'Inactive'])->default('Active');
                }
                if (!Schema::hasColumn('colors', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }

        Schema::create('grn_entries', function (Blueprint $table) {
            $table->id();
            $table->string('grn_number')->unique();
            $table->date('grn_date');
            $table->foreignId('purchase_invoice_id')->constrained('purchase_invoices');
            
            $table->unsignedInteger('supplier_id');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            
            $table->date('supplier_invoice_date');
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('grn_entry_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grn_entry_id')->constrained('grn_entries')->onDelete('cascade');
            $table->unsignedBigInteger('purchase_invoice_item_id')->nullable();
            
            $table->string('art_no')->nullable();
            $table->foreignId('fabric_type_id')->nullable()->constrained('fabric_types');
            
            $table->decimal('qty_ordered', 15, 2)->default(0);
            $table->decimal('qty_received', 15, 2)->default(0);
            $table->decimal('qty_accepted', 15, 2)->default(0);
            $table->decimal('qty_rejected', 15, 2)->default(0);
            $table->decimal('qty_balanced', 15, 2)->default(0);
            
            $table->decimal('rate', 15, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            
            $table->enum('quality_check_status', ['Pass', 'Fail', 'Hold'])->nullable();
            $table->foreignId('store_location_id')->nullable()->constrained('store_locations');
            
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('grn_entry_item_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grn_entry_item_id')->constrained('grn_entry_items')->onDelete('cascade');
            $table->integer('color_id');
            $table->foreign('color_id')->references('id')->on('colors');
            $table->decimal('qty_received', 15, 2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grn_entry_item_variants');
        Schema::dropIfExists('grn_entry_items');
        Schema::dropIfExists('grn_entries');
    }
};
