<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$jobCard = App\Models\JobCardEntry::with([
    'brand',
    'item',
    'serviceProvider',
    'sizeRatio',
    'cuttingSizeRatios',
    'fabricDetails.quantities',
    'fabricDetails.stockEntry',
    'fabricDetails.productionReceipts'
])->find(160);

$additionalBatches = $jobCard->fabricDetails->where('is_additional', 1)
    ->groupBy(function($item) {
        return $item->additional_batch_no ?? ($item->created_at ? $item->created_at->format('Y-m-d H:i') : $item->id);
    })
    ->values();

echo "Batches Count: " . $additionalBatches->count() . "\n";
foreach ($additionalBatches as $idx => $bg) {
    echo "Batch #" . ($bg->first()->additional_batch_no ?? ($idx+1)) . " Fabrics: " . $bg->pluck('art_no')->implode(', ') . " Total Qty: " . $bg->sum('total_qty') . " Total Mtr: " . $bg->sum('mtr') . "\n";
}
