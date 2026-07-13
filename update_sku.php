<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$oldSku = 'BC014801';
$newSku = 'BCORGANICLINEN4801';

\Illuminate\Support\Facades\DB::table('stock_entry_items')->where('sku', $oldSku)->update(['sku' => $newSku]);
\Illuminate\Support\Facades\DB::table('production_receipt_items')->where('sku', $oldSku)->update(['sku' => $newSku]);
\Illuminate\Support\Facades\DB::table('barcode_masters')->where('barcode_no', $oldSku)->update(['barcode_no' => $newSku]);

echo "Done\n";
