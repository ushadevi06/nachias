<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('core_material_planner_settings')) {
            Schema::create('core_material_planner_settings', function (Blueprint $table) {
                $table->id();
                $table->string('art_no')->unique();
                $table->unsignedBigInteger('brand_id')->nullable();
                $table->decimal('daily_consumption', 12, 2)->default(0);
                $table->integer('supplier_lead_time')->default(0);
                $table->decimal('safety_stock', 12, 2)->default(0);
                $table->enum('status', ['Active', 'Inactive'])->default('Active');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('core_material_planner_settings');
    }
};
