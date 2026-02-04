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
        Schema::table('production_services', function (Blueprint $table) {
            $table->dropColumn(['multiplier', 'uom']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_services', function (Blueprint $table) {
            $table->decimal('multiplier', 8, 2)->default(1.00);
            $table->string('uom', 20)->nullable();
        });
    }
};
