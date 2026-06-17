<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('production_service_process_group', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_service_id');
            $table->unsignedBigInteger('process_group_id');
            $table->timestamps();

            $table->foreign('production_service_id', 'ps_pg_ps_id_foreign')
                  ->references('id')
                  ->on('production_services')
                  ->onDelete('cascade');

            $table->foreign('process_group_id', 'ps_pg_pg_id_foreign')
                  ->references('id')
                  ->on('process_groups')
                  ->onDelete('cascade');
        });

        // Copy existing relationship data from production_services table
        $existingServices = DB::table('production_services')
            ->whereNotNull('process_group_id')
            ->get();

        foreach ($existingServices as $service) {
            DB::table('production_service_process_group')->insert([
                'production_service_id' => $service->id,
                'process_group_id' => $service->process_group_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_service_process_group');
    }
};
