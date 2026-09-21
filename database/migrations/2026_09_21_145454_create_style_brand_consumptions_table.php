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
        Schema::create('style_brand_consumptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('style_id')->constrained('styles')->onDelete('cascade');
            $table->foreignId('brand_id')->constrained('brands')->onDelete('cascade');
            $table->decimal('average_consumption', 10, 2)->default(0);
            $table->timestamps();
            $table->unique(['style_id', 'brand_id'], 'style_brand_consumption_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('style_brand_consumptions');
    }
};
