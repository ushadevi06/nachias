<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$order = \App\Models\SalesOrder::where('order_no', '100008840')->first();
if ($order) {
    echo json_encode($order->items()->select('sku', 'item_name', 'art_no')->get());
} else {
    echo 'Order not found';
}
