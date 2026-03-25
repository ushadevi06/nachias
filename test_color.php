<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$seIds = [8, 3]; // Test Stock Entry IDs

$stockItem = \App\Models\StockEntryItem::whereIn('stock_entry_id', $seIds)
    ->whereHas('rawMaterial', function($q) {
        $q->where('store_category_id', 1);
    })
    ->with('grnEntryItem.purchaseInvoiceItem.purchaseOrderItem')
    ->first();

echo "StockItem ID: " . ($stockItem ? $stockItem->id : 'null') . "\n";
if ($stockItem && $stockItem->grnEntryItem) {
    echo "GRN Item ID: " . $stockItem->grnEntryItem->id . "\n";
    if ($stockItem->grnEntryItem->purchaseInvoiceItem) {
        echo "Purchase Invoice Item ID: " . $stockItem->grnEntryItem->purchaseInvoiceItem->id . "\n";
        if ($stockItem->grnEntryItem->purchaseInvoiceItem->purchaseOrderItem) {
            echo "Purchase Order Item ID: " . $stockItem->grnEntryItem->purchaseInvoiceItem->purchaseOrderItem->id . "\n";
            echo "Color ID on PO Item: " . ($stockItem->grnEntryItem->purchaseInvoiceItem->purchaseOrderItem->color_id ?? 'null') . "\n";
        } else {
            echo "No Purchase Order Item attached to Purchase Invoice Item!\n";
        }
    } else {
        echo "No Purchase Invoice Item attached to GRN Item!\n";
    }
} else {
    echo "No GRN Item attached to StockItem!\n";
}
