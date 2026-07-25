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
        Schema::table('document_repositories', function (Blueprint $table) {
            $table->string('reference_no', 100)->nullable()->after('document_name');
        });
        \DB::statement("ALTER TABLE document_repositories MODIFY COLUMN status ENUM('Active', 'Expired', 'Archived') NOT NULL DEFAULT 'Active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_repositories', function (Blueprint $table) {
            $table->dropColumn('reference_no');
        });
        \DB::statement("ALTER TABLE document_repositories MODIFY COLUMN status ENUM('Active', 'Inactive') NOT NULL DEFAULT 'Active'");
    }
};
