<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
auth()->login($user);

// Test rendering history
$controller = new App\Http\Controllers\JobCardEntryController();
$historyView = $controller->additionalQtyHistory(160);
$renderedHistory = $historyView->render();
echo "History rendered successfully! Output length: " . strlen($renderedHistory) . "\n";

// Test rendering batch view
request()->merge(['batch_id' => 160]);
$viewView = $controller->additionalQtyView(160);
$renderedView = $viewView->render();
echo "Batch View rendered successfully! Output length: " . strlen($renderedView) . "\n";
