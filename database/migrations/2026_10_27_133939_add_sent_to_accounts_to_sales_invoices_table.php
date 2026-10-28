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
            $table->boolean('sent_to_accounts')->default(false)->after('kachathu_details');
            $table->unsignedBigInteger('sent_to_accounts_by')->nullable()->after('sent_to_accounts');
            $table->dateTime('sent_to_accounts_at')->nullable()->after('sent_to_accounts_by');

            $table->index('sent_to_accounts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table) {
            $table->dropIndex(['sent_to_accounts']);
            $table->dropColumn(['sent_to_accounts', 'sent_to_accounts_by', 'sent_to_accounts_at']);
        });
    }
};
