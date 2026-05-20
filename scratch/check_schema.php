<?php
require 'c:/xampp/htdocs/nachias/vendor/autoload.php';
$app = require_once 'c:/xampp/htdocs/nachias/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Brand;

$brands = Brand::where('status', 'Active')->get();
print_r($brands->toArray());
