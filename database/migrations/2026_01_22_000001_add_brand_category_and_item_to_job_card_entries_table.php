<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_card_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('job_card_entries', 'brand_category_id')) {
                $table->unsignedBigInteger('brand_category_id')->nullable()->after('size_ratio_id');
                $table->index('brand_category_id');
            }
            if (!Schema::hasColumn('job_card_entries', 'item_id')) {
                $table->unsignedBigInteger('item_id')->nullable()->after('brand_category_id');
                $table->index('item_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('job_card_entries', function (Blueprint $table) {
            if (Schema::hasColumn('job_card_entries', 'item_id')) {
                $table->dropIndex(['item_id']);
                $table->dropColumn('item_id');
            }
            if (Schema::hasColumn('job_card_entries', 'brand_category_id')) {
                $table->dropIndex(['brand_category_id']);
                $table->dropColumn('brand_category_id');
            }
        });
    }
};

