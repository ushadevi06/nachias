<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('job_card_issue_items', function (Blueprint $table) {
            $table->string('barcode_no')->nullable()->after('cost_per_pc');
            $table->text('qrcode_data')->nullable()->after('barcode_no');
        });
    }

    public function down()
    {
        Schema::table('job_card_issue_items', function (Blueprint $table) {
            $table->dropColumn(['barcode_no', 'qrcode_data']);
        });
    }
};
