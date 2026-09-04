<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
auth()->login($user);

$controller = new App\Http\Controllers\JobCardEntryController();
$fabric = App\Models\JobCardFabricDetail::where('job_card_entry_id', 160)->where('is_additional', 1)->first();

echo "Testing with fabric ID: " . $fabric->id . "\n";
request()->merge(['batch_id' => $fabric->id]);
$response = $controller->additional_qty(160);
$html = $response->render();

echo "Rendered length: " . strlen($html) . "\n";
// Check how many Art Nos are present in the table
preg_match_all('/CF10205-\d/', $html, $matches);
echo "Art No matches found in edit page HTML:\n";
print_r(array_unique($matches[0]));
