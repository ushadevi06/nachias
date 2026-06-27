<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$req = new \Illuminate\Http\Request();
$req->merge(['customer_id' => 435]);
$ctrl = new \App\Http\Controllers\SalesInvoiceController();
echo json_encode($ctrl->getCustomerSalesOrders($req)->getData());
