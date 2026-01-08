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
        Schema::table('job_card_images', function (Blueprint $table) {
            $table->dropColumn(['image_path', 'image_type']);
            $table->string('art_no')->nullable()->change();
            $table->string('image')->nullable()->after('art_no');
        });

        DB::statement("ALTER TABLE job_card_images MODIFY COLUMN art_no VARCHAR(255) AFTER job_card_entry_id");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_card_images', function (Blueprint $table) {
            $table->dropColumn('image');
            $table->string('image_path')->after('job_card_entry_id');
            $table->string('image_type')->after('image_path');
        });
    }
};
