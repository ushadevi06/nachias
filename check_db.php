<?php
require 'vendor/autoload.php';
require 'bootstrap/app.php';
$app = app();
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo json_encode(\Illuminate\Support\Facades\Schema::getColumnListing('sales_invoice_items'), JSON_PRETTY_PRINT);
