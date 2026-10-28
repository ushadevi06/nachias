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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('tamil_name', 255)->nullable()->after('name');
            $table->text('tamil_address')->nullable()->after('address_line_3');
            $table->string('tamil_place', 255)->nullable()->after('tamil_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['tamil_name', 'tamil_address', 'tamil_place']);
        });
    }
};
