<?php require 'vendor/autoload.php'; $app = require_once 'bootstrap/app.php'; $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class); $kernel->bootstrap(); 
$items = DB::table('items')->whereNull('name')->orWhere('name', '')->count();
$barcodes = DB::table('barcode_masters')->whereNull('item_name')->orWhere('item_name', '')->count();
echo "Empty names in items: $items\nEmpty names in barcodes: $barcodes\n";
