<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$items = \App\Models\StockEntryItem::where('art_no', 'CF2345-4')
    ->orWhere('sku', 'like', '%234504%')
    ->get();

echo "Matching stock entry items count: " . $items->count() . "\n";
foreach ($items as $si) {
    echo "ID: {$si->id}, SKU: {$si->sku}, finished_item_code: {$si->finished_item_code}, art_no: {$si->art_no}, size: {$si->size}, qty_in: {$si->qty_in}, qty_out: {$si->qty_out}, deleted_at: " . ($si->deleted_at ?? 'NULL') . "\n";
}
