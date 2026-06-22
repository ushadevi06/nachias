<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = new \App\Services\OrderaxeService();
$orders = $service->fetchOrders(0, 5000);

$reflection = new ReflectionClass($service);
$method = $reflection->getMethod('processOrder');
$method->setAccessible(true);

$updated = 0;
foreach ($orders as $order) {
    $method->invoke($service, $order);
    $updated++;
}
echo "Processed $updated historical orders.\n";
