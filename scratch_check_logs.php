<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$log1 = App\Models\Log::find(37466);
$log2 = App\Models\Log::find(37467);

echo "Log 37466 details:\n";
echo "New data: " . json_encode($log1->new_data) . "\n";

echo "\nLog 37467 details:\n";
echo "New data: " . json_encode($log2->new_data) . "\n";

