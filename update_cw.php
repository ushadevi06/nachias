<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

DB::table('stock_entry_items')->where('finished_item_code', 'CW-FS')->update(['finished_item_code' => 'CW-WHT-FS']);
DB::table('production_receipt_items')->where('item_code', 'CW-FS')->update(['item_code' => 'CW-WHT-FS']);
DB::table('barcode_masters')->where('item_code', 'CW-FS')->update(['item_code' => 'CW-WHT-FS']);

DB::table('stock_entry_items')->where('finished_item_code', 'CW-HS')->update(['finished_item_code' => 'CW-WHT-HS']);
DB::table('production_receipt_items')->where('item_code', 'CW-HS')->update(['item_code' => 'CW-WHT-HS']);
DB::table('barcode_masters')->where('item_code', 'CW-HS')->update(['item_code' => 'CW-WHT-HS']);

echo "Done\n";
