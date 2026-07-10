<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$entry = \App\Models\StockEntryItem::where('stock_type', 'finished_goods')->first();
if ($entry) {
    echo json_encode($entry->toArray(), JSON_PRETTY_PRINT);
} else {
    echo 'No entry found';
}
