<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    if (!Schema::hasColumn('purchase_invoice_charges', 'tax_type')) {
        Schema::table('purchase_invoice_charges', function (Blueprint $table) {
            $table->string('tax_type')->default('Post-GST')->after('charge_amount');
        });
        echo "Column 'tax_type' added to 'purchase_invoice_charges' table successfully.\n";
    } else {
        echo "Column 'tax_type' already exists in 'purchase_invoice_charges' table.\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
