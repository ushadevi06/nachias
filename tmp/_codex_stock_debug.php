<?php
require dirname(__DIR__) . "/vendor/autoload.php";
$app = require dirname(__DIR__) . "/bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$controller = app(App\Http\Controllers\PurchaseReportController::class);
$request = Illuminate\Http\Request::create("/", "GET");

$ref = new ReflectionClass($controller);
$getStock = $ref->getMethod("getStockData");
$getStock->setAccessible(true);
$all = $getStock->invoke($controller, 2, $request);

echo "stockRows=" . count($all) . PHP_EOL;
$minMin = null; $maxMin = null; $minClose=null; $maxClose=null;
foreach ($all as $row) {
  $min = $row['min_stock']; $close = $row['closing'];
  $minMin = $minMin===null? $min : min($minMin,$min);
  $maxMin = $maxMin===null? $min : max($maxMin,$min);
  $minClose = $minClose===null? $close : min($minClose,$close);
  $maxClose = $maxClose===null? $close : max($maxClose,$close);
}
echo "min_stock range: {$minMin}..{$maxMin}\n";
echo "closing range: {$minClose}..{$maxClose}\n";
// show any that are close to threshold
$near=[];
foreach ($all as $row) {
  if ($row['min_stock']>0 && $row['closing'] <= $row['min_stock']+1) { $near[]=$row; }
}
echo "nearCount=".count($near)."\n";
if(count($near)) print_r(array_slice($near,0,5));
