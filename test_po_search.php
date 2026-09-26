<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = new App\Http\Controllers\PurchaseOrderController();
auth()->loginUsingId(1);

$tests = ['1,090.95', '1090.95', 'PO-0036', 'CASINO', 'Draft', '269.00'];
foreach ($tests as $term) {
    $req = Illuminate\Http\Request::create('/purchase_orders', 'GET', ['search' => ['value' => $term]]);
    $req->headers->set('X-Requested-With', 'XMLHttpRequest');
    $res = json_decode($controller->index($req)->getContent(), true);
    echo "Search '{$term}' => Found: " . $res['recordsFiltered'] . "\n";
}
