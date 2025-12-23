<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->string('table_name')->nullable()->after('module');
            $table->unsignedBigInteger('record_id')->nullable()->after('table_name');
            $table->json('old_values')->nullable()->after('record_id');
            $table->json('new_values')->nullable()->after('old_values');
            $table->string('ip_address')->nullable()->after('new_values');
            $table->text('user_agent')->nullable()->after('ip_address');
            $table->text('description')->nullable()->after('user_agent');
        });
    }

    public function down(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->dropColumn([
                'table_name',
                'record_id',
                'old_values',
                'new_values',
                'ip_address',
                'user_agent',
                'description',
            ]);
        });
    }
};
