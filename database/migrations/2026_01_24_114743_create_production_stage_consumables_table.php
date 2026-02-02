<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_stage_consumables', function (Blueprint $table) {
            $table->id();
            $table->string('stage'); 
            $table->unsignedBigInteger('raw_material_id');
            $table->decimal('quantity_per_unit', 15, 4)->default(0);
            $table->unsignedBigInteger('uom_id')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('raw_material_id')->references('id')->on('raw_materials');
            $table->foreign('uom_id')->references('id')->on('uoms');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_stage_consumables');
    }
};
