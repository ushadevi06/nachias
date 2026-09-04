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
        if (Schema::hasTable('job_card_fabric_details')) {
            Schema::table('job_card_fabric_details', function (Blueprint $table) {
                $columnsToDrop = [];
                foreach (['lay_meter', 'plies', 'lay_out', 'cut_date'] as $col) {
                    if (Schema::hasColumn('job_card_fabric_details', $col)) {
                        $columnsToDrop[] = $col;
                    }
                }
                if (!empty($columnsToDrop)) {
                    $table->dropColumn($columnsToDrop);
                }
            });
        }

        if (Schema::hasTable('job_card_entries') && Schema::hasColumn('job_card_entries', 'total_cutting_lots')) {
            Schema::table('job_card_entries', function (Blueprint $table) {
                $table->dropColumn('total_cutting_lots');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('job_card_fabric_details')) {
            Schema::table('job_card_fabric_details', function (Blueprint $table) {
                $table->decimal('lay_meter', 10, 2)->nullable();
                $table->integer('plies')->nullable();
                $table->string('lay_out', 100)->nullable();
                $table->date('cut_date')->nullable();
            });
        }

        if (Schema::hasTable('job_card_entries')) {
            Schema::table('job_card_entries', function (Blueprint $table) {
                $table->integer('total_cutting_lots')->default(1);
            });
        }
    }
};
