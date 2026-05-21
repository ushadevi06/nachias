<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$stocks = DB::table('raw_materials')->where('store_category_id', 2)->pluck('min_stock')->toArray();
print_r($stocks);
