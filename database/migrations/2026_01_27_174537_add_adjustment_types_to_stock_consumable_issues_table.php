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
        // For MySQL, we often need to use raw SQL to modify an ENUM
        DB::statement("ALTER TABLE stock_consumable_issues MODIFY COLUMN issue_type ENUM('Consumable Issue', 'Sales Return', 'Stock Adjustment', 'Consumable Adjustment') DEFAULT 'Consumable Issue'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE stock_consumable_issues MODIFY COLUMN issue_type ENUM('Consumable Issue', 'Sales Return') DEFAULT 'Consumable Issue'");
    }
};
