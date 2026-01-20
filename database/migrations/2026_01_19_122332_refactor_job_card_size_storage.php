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
        // 1. Refactor job_card_cutting_size_ratios
        Schema::table('job_card_cutting_size_ratios', function (Blueprint $table) {
            if (Schema::hasColumn('job_card_cutting_size_ratios', 'article_no')) {
                $table->dropColumn('article_no');
            }
        });

        // 2. Refactor job_card_fabric_details (remove hardcoded size columns)
        Schema::table('job_card_fabric_details', function (Blueprint $table) {
            $columns = [
                'fs_36', 'fs_38', 'fs_40', 'fs_42', 'fs_44', 'fs_46',
                'hs_36', 'hs_38', 'hs_40', 'hs_42', 'hs_44', 'hs_46',
                'ex_1', 'ex_2'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('job_card_fabric_details', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        // 3. Create new table for dynamic matrix quantities
        Schema::create('job_card_matrix_quantities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_card_fabric_detail_id');
            $table->string('size');
            $table->decimal('qty_fs', 10, 2)->default(0);
            $table->decimal('qty_hs', 10, 2)->default(0);
            $table->decimal('total_qty', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('job_card_fabric_detail_id', 'fk_jc_matrix_quant_id')
                  ->references('id')->on('job_card_fabric_details')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_card_matrix_quantities');

        Schema::table('job_card_fabric_details', function (Blueprint $table) {
            $table->integer('fs_36')->default(0);
            $table->integer('fs_38')->default(0);
            $table->integer('fs_40')->default(0);
            $table->integer('fs_42')->default(0);
            $table->integer('fs_44')->default(0);
            $table->integer('fs_46')->default(0);
            $table->integer('hs_36')->default(0);
            $table->integer('hs_38')->default(0);
            $table->integer('hs_40')->default(0);
            $table->integer('hs_42')->default(0);
            $table->integer('hs_44')->default(0);
            $table->integer('hs_46')->default(0);
            $table->integer('ex_1')->default(0);
            $table->integer('ex_2')->default(0);
        });

        Schema::table('job_card_cutting_size_ratios', function (Blueprint $table) {
            $table->string('article_no')->nullable()->after('job_card_entry_id');
        });
    }
};
