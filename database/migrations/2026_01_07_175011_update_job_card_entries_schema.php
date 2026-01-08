<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('job_card_entries', function (Blueprint $table) {
            
            // Add reference_no
            if (!Schema::hasColumn('job_card_entries', 'reference_no')) {
                $table->string('reference_no')->nullable()->after('job_card_no');
            }

            // Add size_ratio_id
            if (!Schema::hasColumn('job_card_entries', 'size_ratio_id')) {
                $table->unsignedBigInteger('size_ratio_id')->nullable()->after('process_group_id');
            }

            // Drop cutting_no if exists
            if (Schema::hasColumn('job_card_entries', 'cutting_no')) {
                $table->dropColumn('cutting_no');
            }

            // Add delivery_date if missing
            if (!Schema::hasColumn('job_card_entries', 'delivery_date')) {
                $table->date('delivery_date')->nullable()->after('job_card_date');
            }
        });

        // Change status from ENUM to VARCHAR to accept any status value
        DB::statement("ALTER TABLE job_card_entries MODIFY COLUMN status VARCHAR(255) DEFAULT 'Draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_card_entries', function (Blueprint $table) {
            if (Schema::hasColumn('job_card_entries', 'reference_no')) {
                $table->dropColumn('reference_no');
            }
            if (Schema::hasColumn('job_card_entries', 'size_ratio_id')) {
                $table->dropColumn('size_ratio_id');
            }
        });
        
        // Restore ENUM (optional - you may not want to reverse this)
        DB::statement("ALTER TABLE job_card_entries MODIFY COLUMN status ENUM('Draft', 'Pending', 'In Progress', 'Completed') DEFAULT 'Draft'");
    }
};
