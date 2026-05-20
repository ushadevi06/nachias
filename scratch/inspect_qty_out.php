<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$soItems = \App\Models\SalesOrderItem::where('stock_entry_item_id', 49)
    ->orWhere('sku', 'BC2345043601')
    ->get();

echo "Matching Sales Order Items: " . $soItems->count() . "\n";
foreach ($soItems as $soi) {
    echo "SO ID: {$soi->sale_order_id}, Qty: {$soi->qty}, SKU: {$soi->sku}, Stock Entry Item ID: {$soi->stock_entry_item_id}\n";
    $so = \App\Models\SalesOrder::find($soi->sale_order_id);
    if ($so) {
        echo "  SO Number: {$so->so_no}, Status: {$so->status}\n";
    }
}

$seAdjustments = \DB::table('stock_entry_adjustments')
    ->where('stock_entry_item_id', 49)
    ->get();
echo "Matching Stock Entry Adjustments: " . $seAdjustments->count() . "\n";
foreach ($seAdjustments as $sea) {
    echo "Adjustment ID: {$sea->id}, Qty: {$sea->qty}\n";
}
