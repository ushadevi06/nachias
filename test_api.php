<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$req = Illuminate\Http\Request::create('/get-finished-item-stock?code=BC13723&sleeve_type=Half&size=40', 'GET');
$controller = app(App\Http\Controllers\SalesOrderController::class);
echo json_encode($controller->getFinishedItemStock($req)->getData());
