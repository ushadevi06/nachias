<?php
require dirname(__DIR__) . "/vendor/autoload.php";
$app = require dirname(__DIR__) . "/bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$controller = app(App\Http\Controllers\PurchaseReportController::class);
$request = Illuminate\Http\Request::create("/", "GET");

$ref = new ReflectionClass($controller);
$m = $ref->getMethod("getMinStockData");
$m->setAccessible(true);
$result = $m->invoke($controller, 2, $request);

echo "minStockCount=" . count($result) . PHP_EOL;
if (count($result)) {
    print_r(array_slice($result, 0, 5));
}
