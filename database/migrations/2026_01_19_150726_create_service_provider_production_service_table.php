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
        Schema::create('service_provider_production_service', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_provider_id');
            $table->unsignedBigInteger('production_service_id');
            $table->timestamps();

            // Use shorter custom constraint names to avoid MySQL 64-char limit
            $table->foreign('service_provider_id', 'sp_ps_sp_id_fk')
                  ->references('id')->on('service_providers')->onDelete('cascade');
            $table->foreign('production_service_id', 'sp_ps_ps_id_fk')
                  ->references('id')->on('production_services')->onDelete('cascade');
            
            // Ensure unique combinations
            $table->unique(['service_provider_id', 'production_service_id'], 'sp_ps_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_provider_production_service');
    }
};
