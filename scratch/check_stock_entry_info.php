<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$jc = App\Models\JobCardEntry::with('fabricDetails.stockEntry')->find(160);
foreach ($jc->fabricDetails as $f) {
    echo "Art: " . $f->art_no . " | StockEntry: " . ($f->stockEntry ? json_encode($f->stockEntry->toArray()) : 'none') . "\n";
}
