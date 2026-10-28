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
        Schema::table('sales_invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_invoices', 'ready_for_dispatch')) {
                $table->tinyInteger('ready_for_dispatch')->default(0)->after('kachathu_details');
            }
            if (!Schema::hasColumn('sales_invoices', 'ready_for_dispatch_at')) {
                $table->timestamp('ready_for_dispatch_at')->nullable()->after('ready_for_dispatch');
            }
            if (!Schema::hasColumn('sales_invoices', 'transport_sticker_printed')) {
                $table->tinyInteger('transport_sticker_printed')->default(0)->after('ready_for_dispatch_at');
            }
            if (!Schema::hasColumn('sales_invoices', 'transport_sticker_printed_at')) {
                $table->timestamp('transport_sticker_printed_at')->nullable()->after('transport_sticker_printed');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->dropColumn([
                'ready_for_dispatch',
                'ready_for_dispatch_at',
                'transport_sticker_printed',
                'transport_sticker_printed_at'
            ]);
        });
    }
};

