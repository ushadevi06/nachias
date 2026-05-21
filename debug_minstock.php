<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

echo "=== DEBUG: Accessories Min Stock ===\n\n";

// 1. Check raw_materials with min_stock > 0 for accessories (store_category_id = 2)
$rawMaterials = DB::table('raw_materials')
    ->where('store_category_id', 2)
    ->where('min_stock', '>', 0)
    ->select('id', 'name', 'min_stock', 'store_category_id')
    ->get();

echo "--- Raw Materials with min_stock > 0 (store_category_id=2) ---\n";
echo "Count: " . $rawMaterials->count() . "\n";
foreach ($rawMaterials->take(10) as $rm) {
    echo "  ID: {$rm->id}, Name: {$rm->name}, MinStock: {$rm->min_stock}\n";
}

// 2. Check stock_entry_items for accessories
$stockEntries = DB::table('stock_entry_items')
    ->where('store_category_id', 2)
    ->select('id', 'raw_material_id', 'item_id', 'qty_in', 'qty_out')
    ->limit(10)
    ->get();

echo "\n--- Stock Entry Items (store_category_id=2) - first 10 ---\n";
echo "Count total: " . DB::table('stock_entry_items')->where('store_category_id', 2)->count() . "\n";
foreach ($stockEntries as $se) {
    echo "  ID: {$se->id}, raw_material_id: {$se->raw_material_id}, item_id: {$se->item_id}, qty_in: {$se->qty_in}, qty_out: {$se->qty_out}\n";
}

// 3. Check if stock entries link to raw_materials with min_stock > 0
$linkedEntries = DB::table('stock_entry_items as sei')
    ->join('raw_materials as rm', 'sei.raw_material_id', '=', 'rm.id')
    ->where('sei.store_category_id', 2)
    ->where('rm.min_stock', '>', 0)
    ->select('rm.id as rm_id', 'rm.name', 'rm.min_stock', 
             DB::raw('SUM(sei.qty_in) as total_in'), 
             DB::raw('SUM(sei.qty_out) as total_out'),
             DB::raw('SUM(sei.qty_in) - SUM(sei.qty_out) as net_stock'))
    ->groupBy('rm.id', 'rm.name', 'rm.min_stock')
    ->get();

echo "\n--- Stock entries linked to raw_materials with min_stock > 0 ---\n";
echo "Count: " . $linkedEntries->count() . "\n";
foreach ($linkedEntries as $le) {
    $status = $le->net_stock <= $le->min_stock ? '*** LOW ***' : 'OK';
    echo "  RM ID: {$le->rm_id}, Name: {$le->name}, MinStock: {$le->min_stock}, NetStock: {$le->net_stock} => {$status}\n";
}

// 4. Check via item_id link
$itemLinked = DB::table('stock_entry_items as sei')
    ->leftJoin('raw_materials as rm', 'sei.raw_material_id', '=', 'rm.id')
    ->where('sei.store_category_id', 2)
    ->whereNull('sei.raw_material_id')
    ->select('sei.item_id')
    ->distinct()
    ->limit(10)
    ->get();

echo "\n--- Stock entries with NULL raw_material_id (using item_id instead) ---\n";
echo "Count: " . $itemLinked->count() . "\n";
foreach ($itemLinked as $il) {
    echo "  item_id: {$il->item_id}\n";
}

echo "\n=== DONE ===\n";
