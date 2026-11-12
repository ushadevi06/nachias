<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('transport_bookings') && !Schema::hasColumn('transport_bookings', 'transport_charge')) {
            Schema::table('transport_bookings', function (Blueprint $table) {
                $table->decimal('transport_charge', 14, 2)->default(0)->after('lr_price');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('transport_bookings') && Schema::hasColumn('transport_bookings', 'transport_charge')) {
            Schema::table('transport_bookings', function (Blueprint $table) {
                $table->dropColumn('transport_charge');
            });
        }
    }
};

