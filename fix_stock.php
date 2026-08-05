<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\StockEntryItem;

echo "Starting stock redistribution...\n";

// Find all items where qty_out > qty_in
$problematicItems = DB::table('stock_entry_items')
    ->where('qty_out', '>', DB::raw('qty_in'))
    ->whereNull('deleted_at')
    ->select('sku', 'size', 'sleeve_type', 'color_id')
    ->distinct()
    ->get();

$processedCount = 0;

foreach ($problematicItems as $group) {
    // Get ALL stock entries for this exact product group, ordered by ID
    $query = StockEntryItem::where('sku', $group->sku)
        ->whereNull('deleted_at');
        
    if ($group->size) $query->where('size', $group->size);
    if ($group->sleeve_type) $query->where('sleeve_type', $group->sleeve_type);
    if ($group->color_id) $query->where('color_id', $group->color_id);

    $items = $query->orderBy('id', 'asc')->get();
    
    if ($items->count() <= 1) continue; // No need to redistribute if there's only 1 row

    // Calculate the TOTAL qty_out across all these rows
    $totalOut = $items->sum('qty_out');
    
    // Reset qty_out to 0 for all rows temporarily
    foreach ($items as $item) {
        $item->qty_out = 0;
    }

    // Distribute totalOut sequentially, capping at qty_in for each row
    $remainingOut = $totalOut;
    foreach ($items as $item) {
        if ($remainingOut <= 0) break;
        
        $canTake = min($item->qty_in, $remainingOut);
        $item->qty_out = $canTake;
        $remainingOut -= $canTake;
        $item->save();
    }

    // If there is still remainingOut (meaning overall stock was negative due to past bugs)
    // Dump it on the last item so the math still balances out.
    if ($remainingOut > 0 && $items->count() > 0) {
        $lastItem = $items->last();
        $lastItem->qty_out += $remainingOut;
        $lastItem->save();
    }
    
    $processedCount++;
}

echo "Successfully processed and redistributed {$processedCount} product groups!\n";
