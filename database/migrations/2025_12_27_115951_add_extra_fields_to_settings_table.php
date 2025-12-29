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
        Schema::table('settings', function (Blueprint $table) {
            $table->text('email')->change(); 
            $table->text('toll_free_no')->nullable()->after('phone_number');
            $table->string('working_days')->nullable()->after('cin_no');
            $table->string('opening_time')->nullable()->after('working_days');
            $table->string('closing_time')->nullable()->after('opening_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('email')->change(); 
            $table->dropColumn(['toll_free_no', 'working_days', 'opening_time', 'closing_time']);
        });
    }
};
