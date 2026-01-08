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
        Schema::table('job_card_entries', function (Blueprint $table) {
            $table->string('cutting_no')->nullable()->after('job_card_number');
            $table->enum('washing', ['Yes', 'No'])->default('No')->after('job_card_date');
            $table->string('width')->nullable()->after('washing');
            $table->decimal('mrp', 15, 2)->nullable()->after('width');
            $table->decimal('fs_qty', 15, 2)->nullable()->after('mrp');
            $table->decimal('hs_qty', 15, 2)->nullable()->after('fs_qty');
            $table->string('receipt_store')->nullable()->after('hs_qty');
            $table->string('fit')->nullable()->after('receipt_store');
            $table->string('patti_type')->nullable()->after('fit');
            $table->string('collar_type')->nullable()->after('patti_type');
            $table->string('cuff_type')->nullable()->after('collar_type');
            $table->string('pocket_type')->nullable()->after('cuff_type');
            $table->string('bottom_cut')->nullable()->after('pocket_type');
            $table->unsignedBigInteger('cutting_master_id')->nullable()->after('bottom_cut');
            $table->date('cutting_date')->nullable()->after('cutting_master_id');
            $table->string('cutting_issue_unit')->nullable()->after('cutting_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_card_entries', function (Blueprint $table) {
            $table->dropColumn([
                'cutting_no', 'washing', 'width', 'mrp', 'fs_qty', 'hs_qty',
                'receipt_store', 'fit', 'patti_type', 'collar_type', 'cuff_type',
                'pocket_type', 'bottom_cut', 'cutting_master_id', 'cutting_date',
                'cutting_issue_unit'
            ]);
        });
    }
};
