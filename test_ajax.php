<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$request = Illuminate\Http\Request::create('/warehouse_reports', 'GET', [
    'report_type' => 'brand-stock',
    'draw' => 1,
    'start' => 0,
    'length' => 10,
    'search' => ['value' => ''],
    'from_date' => 'null',
    'to_date' => 'null',
    'brand_id' => 'null',
    'store_id' => 'null'
]);
$request->headers->set('X-Requested-With', 'XMLHttpRequest');

$controller = app()->make(\App\Http\Controllers\WarehouseReportController::class);
$response = $controller->index($request);

echo $response->getContent();
