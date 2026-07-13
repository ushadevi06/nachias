<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$artNo = 'ORGANIC LINEN';

$stockStyle = \DB::table('stock_entry_items')
    ->join('styles', 'stock_entry_items.style_id', '=', 'styles.id')
    ->where('stock_entry_items.art_no', $artNo)
    ->select(
        'styles.code as style_code',
        'styles.style_name'
    )
    ->first();

echo json_encode(['stock_style' => $stockStyle]);
