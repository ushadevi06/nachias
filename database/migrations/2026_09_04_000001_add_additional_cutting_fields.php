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
        if (Schema::hasTable('job_card_entries')) {
            Schema::table('job_card_entries', function (Blueprint $table) {
                if (!Schema::hasColumn('job_card_entries', 'additional_qty')) {
                    $table->integer('additional_qty')->default(0)->after('grand_total_qty');
                }
                if (!Schema::hasColumn('job_card_entries', 'total_cutting_lots')) {
                    $table->integer('total_cutting_lots')->default(1)->after('additional_qty');
                }
            });
        }

        if (Schema::hasTable('job_card_fabric_details')) {
            Schema::table('job_card_fabric_details', function (Blueprint $table) {
                if (!Schema::hasColumn('job_card_fabric_details', 'lot_no')) {
                    $table->integer('lot_no')->default(0)->after('job_card_entry_id');
                }
                if (!Schema::hasColumn('job_card_fabric_details', 'lay_meter')) {
                    $table->decimal('lay_meter', 10, 2)->nullable()->after('lot_no');
                }
                if (!Schema::hasColumn('job_card_fabric_details', 'plies')) {
                    $table->integer('plies')->nullable()->after('lay_meter');
                }
                if (!Schema::hasColumn('job_card_fabric_details', 'lay_out')) {
                    $table->string('lay_out', 100)->nullable()->after('plies');
                }
                if (!Schema::hasColumn('job_card_fabric_details', 'cut_date')) {
                    $table->date('cut_date')->nullable()->after('lay_out');
                }
            });
        }

        if (Schema::hasTable('job_card_cutting_size_ratios')) {
            Schema::table('job_card_cutting_size_ratios', function (Blueprint $table) {
                if (!Schema::hasColumn('job_card_cutting_size_ratios', 'lot_no')) {
                    $table->integer('lot_no')->default(0)->after('job_card_entry_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('job_card_entries')) {
            Schema::table('job_card_entries', function (Blueprint $table) {
                if (Schema::hasColumn('job_card_entries', 'additional_qty')) {
                    $table->dropColumn('additional_qty');
                }
                if (Schema::hasColumn('job_card_entries', 'total_cutting_lots')) {
                    $table->dropColumn('total_cutting_lots');
                }
            });
        }

        if (Schema::hasTable('job_card_fabric_details')) {
            Schema::table('job_card_fabric_details', function (Blueprint $table) {
                $cols = ['lot_no', 'lay_meter', 'plies', 'lay_out', 'cut_date'];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('job_card_fabric_details', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('job_card_cutting_size_ratios')) {
            Schema::table('job_card_cutting_size_ratios', function (Blueprint $table) {
                if (Schema::hasColumn('job_card_cutting_size_ratios', 'lot_no')) {
                    $table->dropColumn('lot_no');
                }
            });
        }
    }
};
