<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$items = \App\Models\StockEntryItem::where(function($q) { 
    $q->where('sku', 'BC22499')->orWhere('barcode', 'BC22499'); 
})->get(['id', 'stock_type', 'qty_in', 'qty_out', 'sku', 'barcode', 'size', 'finished_item_code']);

echo json_encode($items, JSON_PRETTY_PRINT);
