<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\ProductionReportController;

$controller = new ProductionReportController();
$request = Request::create('/production_reports/ajax/production-planning', 'GET', [
    'stage_id' => 1,
    'from_date' => '16-09-2026',
    'to_date' => '16-09-2026',
    'draw' => 1,
    'start' => 0,
    'length' => 25,
]);

$response = $controller->ajaxReportData($request, 'production-planning');
$data = json_decode($response->getContent(), true);

echo "Total records: " . $data['recordsTotal'] . "\n";
echo "Meta: " . json_encode($data['meta']) . "\n";
foreach ($data['data'] as $r) {
    echo "{$r['s_no']}. {$r['name']} - {$r['work']} - Cut: {$r['cut_no']} - Plan: {$r['plan_qty']} - Finish: {$r['completed_qty']}\n";
}
