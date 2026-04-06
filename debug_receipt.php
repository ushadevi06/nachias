<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$id = 6;
$controller = new App\Http\Controllers\ProductionReceiptController();
$request = new Illuminate\Http\Request();
$response = $controller->getJobCardDetails($request, $id);

echo 'Response Status: ' . $response->getStatusCode() . PHP_EOL;
$data = json_decode($response->getContent(), true);

if ($data['success']) {
    echo 'Job Card: ' . $data['data']['job_card_no'] . PHP_EOL;
    foreach ($data['data']['items'] as $item) {
        echo 'Item Code: ' . $item['item_code'] . ' | Description: ' . $item['description'] . PHP_EOL;
    }
} else {
    echo 'Error: ' . $data['message'] . PHP_EOL;
}
