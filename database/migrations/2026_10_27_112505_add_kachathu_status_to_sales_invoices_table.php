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
            $table->string('kachathu_status', 50)->default('pending')->after('checking_status');
            $table->unsignedBigInteger('kachathu_by')->nullable()->after('kachathu_status');
            $table->dateTime('kachathu_at')->nullable()->after('kachathu_by');
            $table->text('kachathu_details')->nullable()->after('kachathu_at'); // stores custom printed tamil details

            $table->index('kachathu_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->dropIndex(['kachathu_status']);
            $table->dropColumn(['kachathu_status', 'kachathu_by', 'kachathu_at', 'kachathu_details']);
        });
    }
};
