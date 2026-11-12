<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('transport_bookings') && !Schema::hasColumn('transport_bookings', 'lr_image')) {
            Schema::table('transport_bookings', function (Blueprint $table) {
                $table->string('lr_image', 500)->nullable()->after('lr_no');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('transport_bookings') && Schema::hasColumn('transport_bookings', 'lr_image')) {
            Schema::table('transport_bookings', function (Blueprint $table) {
                $table->dropColumn('lr_image');
            });
        }
    }
};

