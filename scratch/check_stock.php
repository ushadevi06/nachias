<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$code = 'CD-PLN-F/S';
$items = DB::table('stock_entry_items')->where('finished_item_code', $code)->get();
echo 'FINISHED ITEMS COUNT: ' . count($items) . PHP_EOL;
foreach($items as $i) {
    echo 'ID: ' . $i->id . ' | STOCK TYPE: ' . $i->stock_type . ' | ART NO: ' . $i->art_no . ' | DELETED: ' . ($i->deleted_at ?? 'NULL') . PHP_EOL;
}
