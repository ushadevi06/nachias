<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Fetch the last 100 sales invoices
$invoices = DB::table('sales_invoices')
    ->select('inv_no')
    ->orderBy('id', 'desc')
    ->limit(100)
    ->get();

$invoiceNumbers = [];
foreach ($invoices as $invoice) {
    if (!empty($invoice->inv_no)) {
        $invoiceNumbers[] = $invoice->inv_no;
    }
}

echo "Last 100 Sales Invoice Numbers:\n\n";
echo implode(', ', $invoiceNumbers) . "\n";
