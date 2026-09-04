<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
auth()->login($user);

$jobCard = App\Models\JobCardEntry::find(160);
echo "Job Card planned qty: " . $jobCard->grand_total_qty . ", additional: " . $jobCard->additional_qty . "\n";
