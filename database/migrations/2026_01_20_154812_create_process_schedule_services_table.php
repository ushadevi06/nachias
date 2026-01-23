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
        Schema::create('process_schedule_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('process_schedule_id')->constrained('process_schedules')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('production_services');
            $table->enum('applies_to', ['ALL', 'Full Sleeve', 'Half Sleeve', 'Both']);
            $table->decimal('calculated_qty', 10, 2)->default(0);
            $table->string('uom')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('process_schedule_services');
    }
};
