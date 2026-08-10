<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

print_r(\Schema::getColumnListing('employees'));
print_r(\Schema::getColumnListing('piece_rate_entries'));
print_r(\Schema::getColumnListing('production_receipts'));
