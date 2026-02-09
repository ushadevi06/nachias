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
            // Drop old string columns and add new foreign key columns
            
            // Fit
            $table->dropColumn('fit');
            $table->unsignedBigInteger('fit_id')->nullable()->after('receipt_store_id');
            $table->foreign('fit_id')->references('id')->on('fits')->onDelete('set null');
            
            // Patti Type
            $table->dropColumn('patti_type');
            $table->unsignedBigInteger('patti_type_id')->nullable()->after('fit_id');
            $table->foreign('patti_type_id')->references('id')->on('patti_types')->onDelete('set null');
            
            // Collar Type
            $table->dropColumn('collar_type');
            $table->unsignedBigInteger('collar_type_id')->nullable()->after('patti_type_id');
            $table->foreign('collar_type_id')->references('id')->on('collar_types')->onDelete('set null');
            
            // Cuff Type
            $table->dropColumn('cuff_type');
            $table->unsignedBigInteger('cuff_type_id')->nullable()->after('collar_type_id');
            $table->foreign('cuff_type_id')->references('id')->on('cuff_types')->onDelete('set null');
            
            // Pocket Type
            $table->dropColumn('pocket_type');
            $table->unsignedBigInteger('pocket_type_id')->nullable()->after('cuff_type_id');
            $table->foreign('pocket_type_id')->references('id')->on('pocket_types')->onDelete('set null');
            
            // Bottom Cut
            $table->dropColumn('bottom_cut');
            $table->unsignedBigInteger('bottom_cut_id')->nullable()->after('pocket_type_id');
            $table->foreign('bottom_cut_id')->references('id')->on('bottom_cuts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_card_entries', function (Blueprint $table) {
            // Drop foreign keys and columns
            $table->dropForeign(['fit_id']);
            $table->dropColumn('fit_id');
            $table->string('fit')->nullable();
            
            $table->dropForeign(['patti_type_id']);
            $table->dropColumn('patti_type_id');
            $table->string('patti_type')->nullable();
            
            $table->dropForeign(['collar_type_id']);
            $table->dropColumn('collar_type_id');
            $table->string('collar_type')->nullable();
            
            $table->dropForeign(['cuff_type_id']);
            $table->dropColumn('cuff_type_id');
            $table->string('cuff_type')->nullable();
            
            $table->dropForeign(['pocket_type_id']);
            $table->dropColumn('pocket_type_id');
            $table->string('pocket_type')->nullable();
            
            $table->dropForeign(['bottom_cut_id']);
            $table->dropColumn('bottom_cut_id');
            $table->string('bottom_cut')->nullable();
        });
    }
};
