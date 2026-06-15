<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle($request = Illuminate\Http\Request::capture());

echo "Match by reference_type = 'Customer Collection': " . DB::table('payments')->where('reference_type', 'Customer Collection')->count() . "\n";
echo "Match by payment_type = 'Customer Collection': " . DB::table('payments')->where('payment_type', 'Customer Collection')->count() . "\n";
echo "Match by reference_type = 'Sales Invoice': " . DB::table('payments')->where('reference_type', 'Sales Invoice')->count() . "\n";

echo "Match by reference_type = 'Supplier Payment': " . DB::table('payments')->where('reference_type', 'Supplier Payment')->count() . "\n";
echo "Match by payment_type = 'Supplier Payment': " . DB::table('payments')->where('payment_type', 'Supplier Payment')->count() . "\n";
echo "Match by reference_type = 'Purchase Invoice': " . DB::table('payments')->where('reference_type', 'Purchase Invoice')->count() . "\n";
