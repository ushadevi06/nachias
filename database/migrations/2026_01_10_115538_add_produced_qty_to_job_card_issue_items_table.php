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
        Schema::table('job_card_issue_items', function (Blueprint $table) {
            $table->decimal('produced_qty', 15, 2)->after('average')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_card_issue_items', function (Blueprint $table) {
            $table->dropColumn('produced_qty');
        });
    }
};
