<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::first();
if ($user) {
    \Illuminate\Support\Facades\Auth::login($user);
}

$controller = $app->make(\App\Http\Controllers\HomeController::class);

// 1. Test All
$request = Illuminate\Http\Request::create('/dashboard/cutting-job-cards', 'GET', [
    'start' => 0,
    'length' => 5,
    'filter' => 'all'
]);
$response = $controller->getCuttingJobCardsAjax($request);
$data = $response->getData(true);

echo "--- Filter: All ---\n";
echo "recordsTotal: " . ($data['recordsTotal'] ?? 'N/A') . "\n";
echo "recordsFiltered: " . ($data['recordsFiltered'] ?? 'N/A') . "\n";
echo "counts: " . json_encode($data['counts'] ?? []) . "\n";
echo "rows returned: " . count($data['data'] ?? []) . "\n";
if (!empty($data['data'])) {
    echo "First row job card: " . strip_tags($data['data'][0]['job_card_no']) . "\n";
}

// 2. Test Active
$requestActive = Illuminate\Http\Request::create('/dashboard/cutting-job-cards', 'GET', [
    'start' => 0,
    'length' => 5,
    'filter' => 'active'
]);
$responseActive = $controller->getCuttingJobCardsAjax($requestActive);
$dataActive = $responseActive->getData(true);
echo "\n--- Filter: Active ---\n";
echo "recordsTotal: " . ($dataActive['recordsTotal'] ?? 'N/A') . "\n";
echo "recordsFiltered: " . ($dataActive['recordsFiltered'] ?? 'N/A') . "\n";

// 3. Test Completed
$requestComp = Illuminate\Http\Request::create('/dashboard/cutting-job-cards', 'GET', [
    'start' => 0,
    'length' => 5,
    'filter' => 'completed'
]);
$responseComp = $controller->getCuttingJobCardsAjax($requestComp);
$dataComp = $responseComp->getData(true);
echo "\n--- Filter: Completed ---\n";
echo "recordsTotal: " . ($dataComp['recordsTotal'] ?? 'N/A') . "\n";
echo "recordsFiltered: " . ($dataComp['recordsFiltered'] ?? 'N/A') . "\n";

// 4. Test Search
$requestSearch = Illuminate\Http\Request::create('/dashboard/cutting-job-cards', 'GET', [
    'start' => 0,
    'length' => 5,
    'filter' => 'all',
    'search' => ['value' => 'CASINO BRAVO']
]);
$responseSearch = $controller->getCuttingJobCardsAjax($requestSearch);
$dataSearch = $responseSearch->getData(true);
echo "\n--- Search: CASINO BRAVO ---\n";
echo "recordsFiltered: " . ($dataSearch['recordsFiltered'] ?? 'N/A') . "\n";
echo "rows returned: " . count($dataSearch['data'] ?? []) . "\n";

