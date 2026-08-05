<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$items = Illuminate\Support\Facades\DB::table('stock_entry_items')
    ->where('sku', 'BC13723')
    ->where('size', '40')
    ->where('sleeve_type', 'Half')
    ->where('stock_type', 'finished_goods')
    ->whereNull('deleted_at')
    ->select('finished_item_code', 'qty_in', 'qty_out', Illuminate\Support\Facades\DB::raw('(qty_in - qty_out) as balance'))
    ->get();

foreach ($items as $item) {
    echo "Item Code: {$item->finished_item_code} | In: {$item->qty_in} | Out: {$item->qty_out} | Math (In-Out): {$item->balance}\n";
}
