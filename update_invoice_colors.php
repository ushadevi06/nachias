<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$invoiceItems = \App\Models\SalesInvoiceItem::whereNull('api_color')->with('salesInvoice')->get();
$count = 0;
foreach ($invoiceItems as $invItem) {
    if (!$invItem->salesInvoice) continue;
    
    $soIds = $invItem->salesInvoice->so_ids;
    if (empty($soIds) && !empty($invItem->salesInvoice->so_id)) {
        $soIds = [$invItem->salesInvoice->so_id];
    } else {
        $soIds = is_string($soIds) ? json_decode($soIds, true) : $soIds;
    }
    
    if (empty($soIds)) continue;

    $query = \App\Models\SalesOrderItem::whereIn('sale_order_id', $soIds);
    if (!empty($invItem->sku)) {
        $query->where('sku', $invItem->sku);
    } else {
        $query->where('art_no', $invItem->art_no)
              ->where('color_id', $invItem->color_id)
              ->where('size', $invItem->size);
    }

    $soItem = $query->first();

    if ($soItem && !empty($soItem->api_color)) {
        $invItem->api_color = $soItem->api_color;
        $invItem->save();
        $count++;
    }
}
echo "Updated $count sales invoice items.\n";
