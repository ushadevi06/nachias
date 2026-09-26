<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$search = 'CASINO';
$count = App\Models\PurchaseOrder::whereHas('items.brand', function ($q) use ($search) {
    $q->where('brand_name', 'like', "%{$search}%");
})->count();

echo "PO count with brand 'CASINO': {$count}\n";
